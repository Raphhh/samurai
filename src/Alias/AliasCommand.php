<?php
namespace Samurai\Alias;

use Samurai\Alias\Task\Factory\AliasManagementTaskFactory;
use Samurai\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AliasCommand
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasCommand extends Command
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
            ->addArgument(
                'source',
                InputArgument::OPTIONAL,
                'bootstrap source'
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
        return AliasManagementTaskFactory::create($input, $this->getServices());
    }
}

