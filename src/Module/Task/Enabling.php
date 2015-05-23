<?php
namespace Samurai\Module\Task;

use Samurai\Task\ITask;
use Samurai\Task\Task;
use SimilarText\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Enabling
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Enabling extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument('name');
        if(!$this->getService('module_manager')->has($moduleName)){
            $textFinder = new Finder($moduleName, array_keys($this->getService('module_manager')->getAll()->getArrayCopy()));
            $output->writeln(sprintf(
                '<error>Module "%s" not found! Did you mean "%s"?</error>',
                $moduleName,
                $textFinder->first()
            ));
            return ITask::BLOCKING_ERROR_CODE;
        }

        $module = $this->getService('module_manager')->get($moduleName);
        $module->setIsEnable($input->getArgument('action') === 'enable');
        $this->getService('module_manager')->modify($moduleName, $module);
        $output->writeln(sprintf('<info>Module "%s" modified</info>', $moduleName));
        return ITask::NO_ERROR_CODE;
    }
}
