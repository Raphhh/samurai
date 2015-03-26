<?php
namespace Samurai\Project\Question;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class KeywordsQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class KeywordsQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $answer = $this->ask(
            $input,
            $output,
            new SimpleQuestion('<question>Enter your project keywords (comma separated):</question>')
        );

        $this->setAnswer($answer);

        return true;
    }

    /**
     * @param $answer
     */
    private function setAnswer($answer)
    {
        if ($answer) {
            $this->getProject()->setKeywords(array_map('trim', explode(',', $answer)));
        } else {
            $this->getProject()->setKeywords([]);
        }
    }
}
