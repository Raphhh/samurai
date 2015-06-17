<?php
namespace Samurai\Module\Task;

use Samurai\Module\Module;
use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class Installing
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Installing extends Task
{
    /**
     * @var array
     */
    private $modules = [
        'git' => [
            'package' => 'raphhh/samurai-module-git',
            'version' => '@dev',
        ],
        'cleaner' => [
            'package' => 'raphhh/samurai-module-cleaner',
            'version' => '@dev',
        ],
    ];

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getArgument('name') && !$input->getArgument('package')) {
            throw new \InvalidArgumentException('bootstrap param is mandatory for this action');
        }

        if($input->getArgument('name') && $input->getArgument('package')){
            return $this->installModule($input, $output);
        }

        return $this->installModules($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    private function installModules(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting modules installation</info>');

        foreach($this->modules as $name => $moduleData){

            if($this->getService('module_manager')->getByPackage($moduleData['package'])->count()){

                $output->writeln(sprintf('<info>Module "%s" already installed</info>', $moduleData['package']));
                continue;
            }

            if($this->confirmInstall($input, $output, $moduleData['package'])){
                $this->getService('module_procedure')->setOutput($output);
                $this->getService('module_procedure')->import($this->buildModule($name, $moduleData));
            }
        }
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $modulePackage
     * @return bool
     */
    private function confirmInstall(InputInterface $input, OutputInterface $output, $modulePackage)
    {
        return $this->getService('helper_set')->get('question')->ask(
            $input,
            $output,
            $this->buildQuestion($modulePackage)
        );
    }

    /**
     * @param string $modulePackage
     * @return ConfirmationQuestion
     */
    private function buildQuestion($modulePackage)
    {
        return new ConfirmationQuestion(
            sprintf(
                '<question>Do you want to install the module "%s"</question>[y]',
                $modulePackage
            )
        );
    }

    /**
     * @param $name
     * @param array $moduleData
     * @return Module
     */
    private function buildModule($name, array $moduleData)
    {
        $module = new Module();
        $module->setName($name);
        $module->setPackage($moduleData['package']);
        $module->setVersion($moduleData['version']);
        $module->setIsEnable(true);
        return $module;
    }

    /**
     * @param $input
     * @param $output
     * @return bool
     */
    private function installModule($input, $output)
    {
        $task = new Saving($this->getServices());
        return $task->execute($input, $output);
    }
}
