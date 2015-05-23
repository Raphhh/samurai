<?php
namespace Samurai\Module\Task;

use Samurai\Module\Module;
use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class Saving
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Saving extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $module = $this->buildModuleFromInput($input);
        if($this->canBeSaved($input, $output, $module)){
            $this->getService('module_procedure')->setOutput($output);
            if(!$this->getService('module_procedure')->import($module)){
                return ITask::BLOCKING_ERROR_CODE;
            }
        }
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param InputInterface $input
     * @return Module
     */
    private function buildModuleFromInput(InputInterface $input)
    {
        $module = new Module();
        $module->setName($input->getArgument('name'));
        $module->setPackage($input->getArgument('package'));
        $module->setVersion($input->getArgument('version'));
        $module->setDescription($input->getArgument('description'));
        $module->setSource($input->getArgument('source'));
        $module->setIsEnable(true);
        return $module;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Module $newModule
     * @return bool
     */
    private function canBeSaved(InputInterface $input, OutputInterface $output, Module $newModule)
    {
        return !$this->getService('module_manager')->has($newModule->getName())
            || $this->confirmOverride($input, $output, $this->getInitialModule($newModule), $newModule);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Module $oldModule
     * @param Module $newModule
     * @return mixed
     */
    private function confirmOverride(InputInterface $input, OutputInterface $output, Module $oldModule, Module $newModule)
    {
        return $this->getService('helper_set')->get('question')->ask(
            $input,
            $output,
            $this->buildQuestion($oldModule, $newModule)
        );
    }

    /**
     * @param Module $oldModule
     * @param Module $newModule
     * @return ConfirmationQuestion
     * @internal param Module $module
     */
    private function buildQuestion(Module $oldModule, Module $newModule)
    {
        return new ConfirmationQuestion(
            sprintf(
                '<question>Do you want to override the module "%s" with "%s"?</question>[y]',
                trim($oldModule->getPackage() . ' ' . $oldModule->getVersion()),
                trim($newModule->getPackage() . ' ' . $newModule->getVersion())
            )
        );
    }

    /**
     * @param Module $newModule
     * @return Module
     */
    private function getInitialModule(Module $newModule)
    {
        return $this->getService('module_manager')->get($newModule->getName());
    }
}
