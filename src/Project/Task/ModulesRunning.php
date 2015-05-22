<?php
namespace Samurai\Project\Task;

use Samurai\Module\Task\Running;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ModulesRunning
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesRunning extends Running
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getOption('no-module')){
            return ITask::NO_ERROR_CODE;
        }
        return $this->runModules($input, $output, $this->getService('module_manager')->getAll());
    }
}
