<?php
namespace Samurai\Project\Question;

use Samurai\alias\Alias;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Class BootstrapQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class BootstrapQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getArgument('bootstrap')){
            $this->setFromArgs($input);
        }else{
            $this->setFromAlias($this->askForAlias($input, $output));
        }
        return (bool)$this->getProject()->getBootstrapName();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Alias
     */
    private function askForAlias(InputInterface $input, OutputInterface $output)
    {
        return $this->ask(
            $input,
            $output,
            new ChoiceQuestion(
                '<question>Choose a bootstrap:</question>',
                $this->getService('alias_manager')->getAll()
            )
        );
    }

    /**
     * @param Alias $alias
     */
    private function setFromAlias(Alias $alias)
    {
        $this->getProject()->setBootstrapName($alias->getBootstrap());
        $this->getProject()->setBootstrapVersion($alias->getVersion());
    }

    /**
     * @param InputInterface $input
     */
    private function setFromArgs(InputInterface $input)
    {
        if ($this->getService('alias_manager')->has($input->getArgument('bootstrap'))) {
            $this->setFromAlias($this->getService('alias_manager')->get($input->getArgument('bootstrap')));
        } else {
            $this->getProject()->setBootstrapName($input->getArgument('bootstrap'));
            $this->getProject()->setBootstrapVersion($input->getArgument('version'));
        }
    }
}
