<?php
namespace Samurai\Alias\Task;

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
            return false;
        }

        if($this->confirmRemove($input, $output, $aliasName)){
            $this->getService('alias_manager')->remove($aliasName);
        }
        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $aliasName
     * @return bool
     */
    private function confirmRemove(InputInterface $input, OutputInterface $output, $aliasName)
    {
        return $this->getService('helper_set')->get('question')->ask(
            $input,
            $output,
            $this->buildQuestion($aliasName)
        );
    }

    /**
     * @param string $aliasName
     * @return ConfirmationQuestion
     */
    private function buildQuestion($aliasName)
    {
        return new ConfirmationQuestion(
            sprintf(
                '<question>Do you want to remove the bootstrap "%s"</question>[y]',
                $aliasName
            )
        );
    }
}
