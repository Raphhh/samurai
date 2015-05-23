<?php
namespace Samurai;

use Balloon\Factory\BalloonFactory;
use PHPGit\Git;
use Pimple\Container;
use Puppy\Config\Config;
use Samurai\Alias\AliasCommand;
use Samurai\Module\Factory\ModuleManagerFactory;
use Samurai\Module\ModuleCommand;
use Samurai\Alias\AliasManagerFactory;
use Samurai\Module\ModuleProcedure;
use Samurai\Module\ModulesSorter;
use Samurai\Project\Composer\Composer;
use Samurai\Project\NewCommand;
use Samurai\Project\Project;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TRex\Cli\Executor;

/**
 * Class Samurai
 * main console
 *
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Samurai
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var Container
     */
    private $services;

    /**
     * @param Application $application
     * @param Container $services
     */
    public function __construct(Application $application, Container $services = null)
    {
        $application->setName('Samurai console');
        $application->setVersion('0.0.0');

        $this->setApplication($application);
        $this->setServices($services ? : $this->buildServices());

        $this->initCommands();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return $this->getApplication($input, $output)->run();
    }

    /**
     * Getter of $application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Getter of $services
     *
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Setter of $services
     *
     * @param Container $services
     */
    public function setServices(Container $services)
    {
        $this->services = $services;
    }

    /**
     *
     */
    private function initCommands()
    {
        $this->getApplication()->add(new NewCommand($this->getServices()));
        $this->getApplication()->add(new AliasCommand($this->getServices()));
        $this->getApplication()->add(new ModuleCommand($this->getServices()));
    }

    /**
     * Setter of $application
     *
     * @param Application $application
     */
    private function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Container
     */
    private function buildServices()
    {
        $application = $this->getApplication();

        $services = new Container();

        $services['project'] = function () {
            return new Project();
        };

        $services['executor'] = function () {
            return new Executor();
        };

        $services['composer'] = function (Container $services) {
            return new Composer($services['executor'], new BalloonFactory());
        };

        $services['helper_set'] = function () use ($application) {
            return $application->getHelperSet();
        };

        $services['config'] = function () {
            return new Config('');
        };

        $services['alias_manager'] = function (Container $services) {
            $factory = new AliasManagerFactory();
            return $factory->createFromConfig($services['config']);
        };

        $services['module_manager'] = function (Container $services) {
            $factory = new ModuleManagerFactory();
            return $factory->create($services['config']['module.path']);
        };

        $services['module_procedure'] = function (Container $services) {
            return new ModuleProcedure(
                $services['module_manager'],
                $services['composer'],
                $services['balloon_factory'],
                new ModulesSorter()
            );
        };

        $services['balloon_factory'] = function () {
            return new BalloonFactory();
        };

        $services['git'] = function () {
            return new Git();
        };

        return $services;
    }
}
