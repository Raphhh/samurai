<?php
namespace Samurai\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Planner extends \ArrayObject implements ITask
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach($this as $task){
            if(!$task->execute($input, $output)){
                return false;
            }
        }
        return true;
    }
}
