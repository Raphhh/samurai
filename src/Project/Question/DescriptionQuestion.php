<?php
namespace Samurai\Project\Question;

use Samurai\Task\ITask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class DescriptionQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DescriptionQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $question = new SimpleQuestion('<question>Enter your project description:</question>');
        $question->setValidator($this->buildValidator());
        $question->setMaxAttempts(3);

        $this->getProject()->setDescription($this->ask(
            $input,
            $output,
            $question
        ));

        return $this->getProject()->getDescription() ? ITask::NO_ERROR_CODE : ITask::BLOCKING_ERROR_CODE;
    }

    /**
     * @return callable
     */
    private function buildValidator()
    {
        return function ($answer) {
            if(!$answer){
                throw new \RuntimeException('Error: description is required');
            }
            return $answer;
        };
    }
}
