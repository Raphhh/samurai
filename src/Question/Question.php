<?php
namespace Samurai\Question;

use Pimple\Container;
use Samurai\Service\ServiceWorker;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class Question
 * @package Samurai\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
abstract class Question implements ITask
{
    use ServiceWorker;

    /**
     * @param Container $services
     */
    public function __construct(Container $services)
    {
        $this->setServices($services);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param SimpleQuestion $question
     * @return mixed
     */
    protected function ask(InputInterface $input, OutputInterface $output, SimpleQuestion $question)
    {
        return $this->getService('question')->ask($input, $output, $question);
    }
}
