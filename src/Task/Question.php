<?php
namespace Samurai\Task;

use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class Question
 * @package Samurai\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
abstract class Question extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param SimpleQuestion $question
     * @return mixed
     */
    protected function ask(InputInterface $input, OutputInterface $output, SimpleQuestion $question)
    {
        return $this->getService('helper_set')->get('question')->ask($input, $output, $question);
    }
}
