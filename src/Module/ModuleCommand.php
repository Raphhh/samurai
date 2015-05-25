<?php
namespace Samurai\Module;

use Samurai\Command\Command;
use Samurai\Module\Task\Factory\ModuleManagementTaskFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ModuleCommand
 * @package Samurai\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleCommand extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('module')
            ->setDescription('Handles Samurai modules')
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'sub-command'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'module name'
            )
            ->addArgument(
                'package',
                InputArgument::OPTIONAL,
                'package name'
            )
            ->addArgument(
                'version',
                InputArgument::OPTIONAL,
                'package version'
            )
            ->addArgument(
                'description',
                InputArgument::OPTIONAL,
                'bootstrap description'
            )
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'bootstrap source'
            )
            ->setHelp('See the documentation for more info: https://github.com/Raphhh/samurai');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getTask($input)->execute($input, $output);
        $this->getService('module_manager')->flush();
    }

    /**
     * @param InputInterface $input
     * @return \Samurai\Task\ITask
     */
    private function getTask(InputInterface $input)
    {
        return ModuleManagementTaskFactory::create($input, $this->getServices());
    }

}
