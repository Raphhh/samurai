<?php
namespace Samurai\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface Command
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
interface ITask
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output);
}
