<?php
namespace Samurai\Module\Task;

use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Task\ITask;
use Samurai\Task\Task;
use SimilarText\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Listing
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Listing extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getArgument('name')){
            return $this->listModule($input->getArgument('name'), $output);
        }
        return $this->listModules($output);
    }

    /**
     * @param $moduleName
     * @param OutputInterface $output
     * @return int
     */
    private function listModule($moduleName, OutputInterface $output)
    {
        if(!$this->getService('module_manager')->has($moduleName)){
            $textFinder = new Finder($moduleName, array_keys($this->getService('module_manager')->getAll()->getArrayCopy()));
            $output->writeln(sprintf(
                '<error>Module "%s" not found! Did you mean "%s"?</error>',
                $moduleName,
                $textFinder->first()
            ));
            return ITask::BLOCKING_ERROR_CODE;
        }
        $output->writeln($this->mapModule($this->getService('module_manager')->get($moduleName)));
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param OutputInterface $output
     * @return int
     */
    private function listModules(OutputInterface $output)
    {
        $output->writeln(sprintf("<info>%d module(s) set:</info>\n", count($this->getService('module_manager')->getAll())));
        $output->writeln($this->mapModules($this->getService('module_manager')->getAll()));
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param Modules $modules
     * @return string
     */
    private function mapModules(Modules $modules)
    {
        $result = '';
        foreach($modules as $name => $module){
            $result .= $this->mapModule($module) . "\n\n";
        }
        return trim($result);
    }

    /**
     * @param Module $module
     * @return string
     */
    private function mapModule(Module $module)
    {
        $result = '';
        foreach ($module->toArray() as $property => $value) {
            if (is_scalar($value) || is_null($value)) {
                $result .= "$property: $value\n";
            }
        }
        return trim($result);
    }
}
