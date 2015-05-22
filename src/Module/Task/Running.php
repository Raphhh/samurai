<?php
namespace Samurai\Module\Task;

use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Module\Planner\ModulePlannerBuilder;
use Samurai\Module\Planner\ModulesPlannerBuilder;
use Samurai\Module\Planner\PlannerAdapter;
use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Running
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Running extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getArgument('name')){
            if(!$this->getService('module_manager')->has($input->getArgument('name'))){
                $output->writeln(sprintf('<error>Module "%s" not found!</error>', $input->getArgument('name')));
                return ITask::BLOCKING_ERROR_CODE;
            }
            return $this->runModule($input, $output, $this->getService('module_manager')->get($input->getArgument('name')));
        }
        return $this->runModules($input, $output, $this->getService('module_manager')->getAll());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Modules $modules
     * @return int|null
     */
    private function runModules(InputInterface $input, OutputInterface $output, Modules $modules)
    {
        $output->writeln(sprintf('<info>Running %d module(s)</info>', count($modules)));
        $planner = new PlannerAdapter(new ModulesPlannerBuilder($this->getServices(), $modules));
        return $planner->execute($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Module $module
     * @return int|null
     */
    private function runModule(InputInterface $input, OutputInterface $output, Module $module)
    {
        $output->writeln('<info>Running the module "'.$module->getName().'"</info>');
        $planner = new PlannerAdapter(new ModulePlannerBuilder($this->getServices(), $module));
        return $planner->execute($input, $output);
    }
}
