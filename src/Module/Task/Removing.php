<?php
namespace Samurai\Module\Task;

use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class Removing
 * @package Samurai\module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Removing extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('name');
        if(!$this->getService('module_manager')->has($moduleName)){
            $output->writeln(sprintf('<error>Error: no module "%s" found</error>', $moduleName));
            return ITask::BLOCKING_ERROR_CODE;
        }

        if($this->confirmRemove($input, $output, $moduleName)){
            $this->getService('module_procedure')->setOutput($output);
            $this->getService('module_procedure')->remove($this->getService('module_manager')->get($moduleName));
        }
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $moduleName
     * @return bool
     */
    private function confirmRemove(InputInterface $input, OutputInterface $output, $moduleName)
    {
        return $this->getService('helper_set')->get('question')->ask(
            $input,
            $output,
            $this->buildQuestion($moduleName)
        );
    }

    /**
     * @param string $moduleName
     * @return ConfirmationQuestion
     */
    private function buildQuestion($moduleName)
    {
        return new ConfirmationQuestion(
            sprintf(
                '<question>Do you want to remove the module "%s"</question>[y]',
                $moduleName
            )
        );
    }
}
