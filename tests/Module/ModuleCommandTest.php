<?php
namespace Samurai\Module;

use Balloon\Factory\BalloonFactory;
use Pimple\Container;
use Puppy\Config\Config;
use Samurai\Module\Factory\ModuleManagerFactory;
use Samurai\Project\Composer\Composer;
use Samurai\Samurai;
use Samurai\Task\ITask;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use TRex\Cli\Executor;

/**
 * Class ModuleCommandTest
 * @package Samurai\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteSave()
    {

        $executor = $this->getMock('TRex\Cli\Executor');
        $executor->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(0));

        $samurai = new Samurai(new Application(), $this->provideServices($executor));

        $this->assertNull($samurai->getServices()['module_manager']->get('test'));


        $command = $samurai->getApplication()->find('module');
        $commandTester = new CommandTester($command);
        $this->assertSame(ITask::NO_ERROR_CODE, $commandTester->execute([
            'command' => $command->getName(),
            'action' => 'install',
            'name' => 'test',
            'package' => 'vendor/package',
            'version' => '@stable',
            'description' => 'description',
        ]));

        $this->assertNotNull($samurai->getServices()['module_manager']->get('test'));
        $this->assertSame(
            "Starting installation of vendor/package\nSorting modules\n",
            $commandTester->getDisplay(true)
        );
    }

    /**
     * @depends testExecuteSave
     */
    public function testExecuteListAll()
    {
        $samurai = new Samurai(new Application());

        $this->assertNotNull($samurai->getServices()['module_manager']->get('test'));

        $command = $samurai->getApplication()->find('module');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'action' => 'list',
        ]);

        $this->assertContains(
            "name: test\ndescription: description\npackage: vendor/package\nversion: @stable\nsource: \nisEnable: 1\n",
            $commandTester->getDisplay(true)
        );
    }

    /**
     * @depends testExecuteListAll
     */
    public function testExecuteUpdate()
    {

        $executor = $this->getMock('TRex\Cli\Executor');
        $executor->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(0));

        $samurai = new Samurai(new Application(), $this->provideServices($executor));

        $this->assertNotNull($samurai->getServices()['module_manager']->get('test'));

        $command = $samurai->getApplication()->find('module');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'action' => 'update',
            'name' => 'test',
        ]);

        $this->assertSame(
            "Updating vendor/package\nSorting modules\n",
            $commandTester->getDisplay(true)
        );
    }

    /**
     * @depends testExecuteUpdate
     */
    public function testExecuteRemove()
    {

        $executor = $this->getMock('TRex\Cli\Executor');
        $executor->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(0));

        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));

        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true));

        $samurai = new Samurai(new Application(), $this->provideServices($executor, $questionHelper));

        $this->assertNotNull($samurai->getServices()['module_manager']->get('test'));

        $command = $samurai->getApplication()->find('module');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'action' => 'rm',
            'name' => 'test',
        ]);

        $this->assertSame(
            "Removing vendor/package\nSorting modules\n",
            $commandTester->getDisplay(true)
        );
    }

    public function testExecuteInstall()
    {
        $executor = $this->getMock('TRex\Cli\Executor');
        $executor->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(0));

        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));

        $questionHelper->expects($this->any())
            ->method('ask')
            ->will($this->returnValue(true));

        $samurai = new Samurai(new Application(), $this->provideServices($executor, $questionHelper));

        $command = $samurai->getApplication()->find('module');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'action' => 'install',
        ]);

        $this->assertContains(
            "Starting modules installation\n",
            $commandTester->getDisplay(true)
        );
    }

    private function provideServices(Executor $executor, QuestionHelper $questionHelper = null)
    {
        $services = new Container();

        $services['executor'] = function () use ($executor){
            return $executor;
        };

        $services['composer'] = function (Container $services) {
            return new Composer($services['executor'], new BalloonFactory());
        };

        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        $services['config'] = function () {
            return new Config('');
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

        return $services;
    }

}
