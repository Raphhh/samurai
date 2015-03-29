<?php
namespace Samurai\Project\Question;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class NameQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class NameQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getProject()->setName($this->ask($input, $output, $this->buildQuestion()));
        return (bool)$this->getProject()->getName();
    }

    /**
     * @return SimpleQuestion
     */
    private function buildQuestion()
    {
        $question = new SimpleQuestion('<question>Enter your project name (<vendor>/<package>):</question>');
        $question->setValidator($this->buildValidator());
        $question->setMaxAttempts(3);
        return $question;
    }

    /**
     * @return callable
     */
    private function buildValidator()
    {
        return function ($answer) {
            if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $answer)) {
                throw new \RuntimeException('Error: format not valid');
            }
            return $answer;
        };
    }
}
