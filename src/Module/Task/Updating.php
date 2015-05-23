<?php
namespace Samurai\Module\Task;

use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Updating
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Updating extends Task
{

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('name'); //todo should call the task for all modules!
        if(!$this->getService('module_manager')->has($moduleName)){
            $output->writeln(sprintf('<error>Error: no module "%s" found</error>', $moduleName));
            return ITask::BLOCKING_ERROR_CODE;
        }

        $this->getService('module_procedure')->setOutput($output);
        $this->getService('module_procedure')->update($this->getService('module_manager')->get($moduleName));
        return ITask::NO_ERROR_CODE;
    }
}
