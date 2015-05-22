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
    const NO_ERROR_CODE = 0;
    const BLOCKING_ERROR_CODE = 1;
    const NON_BLOCKING_ERROR_CODE = 2;


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output);
}
