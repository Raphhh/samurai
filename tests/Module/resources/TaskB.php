<?php
namespace Samurai\Module\resources;

use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TaskB
 * @package Samurai\Module\Planner\resources
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class TaskB extends Task
{

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('B');
        return ITask::NO_ERROR_CODE;
    }
}