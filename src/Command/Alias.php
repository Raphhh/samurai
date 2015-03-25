<?php
namespace Samurai\Command;

use Samurai\Alias\Task\Factory\AliasManagementTaskFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandLine
 * @package Samurai\Composer
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Alias extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('alias')
            ->setDescription('Handles bootstrap alias')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'alias name'
            )
            ->addArgument(
                'bootstrap',
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
            ->addOption(
                'global',
                'g',
                InputOption::VALUE_NONE,
                'Display global alias'
            )
            ->addOption(
                'local',
                'l',
                InputOption::VALUE_NONE,
                'Display local alias'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getTask($input)->execute($input, $output);
    }

    /**
     * @param InputInterface $input
     * @return \Samurai\Task\ITask
     */
    private function getTask(InputInterface $input)
    {
        return (new AliasManagementTaskFactory())->create($input, $this->getServices());
    }
}

