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
     * @var array
     */
    public static $actions = [
        'save',
        'list',
        'rm'
    ];

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('alias')
            ->setDescription('Handles bootstrap alias')
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'sub-command: ' . json_encode(self::$actions)
            )
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
        $this->getService('alias_manager')->flush();
    }

    /**
     * @param InputInterface $input
     * @return \Samurai\Task\ITask
     */
    private function getTask(InputInterface $input)
    {
        $factory = new AliasManagementTaskFactory();
        return $factory->create($input, $this->getServices());
    }
}

