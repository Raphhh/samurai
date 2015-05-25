<?php
namespace Samurai\Alias\Task;

use Samurai\Task\ITask;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class Removing
 * @package Samurai\Alias\Task
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
        $aliasName = $input->getArgument('name');
        if(!$this->getService('alias_manager')->hasLocal($aliasName)){
            $output->writeln(sprintf('<error>Error: no alias "%s" found</error>', $aliasName));
            return ITask::BLOCKING_ERROR_CODE;
        }
        $alias = $this->getService('alias_manager')->get($aliasName);
        if($this->confirmRemove($input, $output, $alias->getPackage())){
            $this->getService('alias_manager')->remove($aliasName);
        }
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $aliasPackage
     * @return bool
     */
    private function confirmRemove(InputInterface $input, OutputInterface $output, $aliasPackage)
    {
        return $this->getService('helper_set')->get('question')->ask(
            $input,
            $output,
            $this->buildQuestion($aliasPackage)
        );
    }

    /**
     * @param string $aliasPackage
     * @return ConfirmationQuestion
     */
    private function buildQuestion($aliasPackage)
    {
        return new ConfirmationQuestion(
            sprintf(
                '<question>Do you want to remove the bootstrap "%s"</question>[y]',
                $aliasPackage
            )
        );
    }
}
