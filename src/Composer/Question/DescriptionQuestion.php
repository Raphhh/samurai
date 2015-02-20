<?php
namespace Samurai\Composer\Question;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class DescriptionQuestion
 * @package Samurai\Composer\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DescriptionQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getProject()->setDescription($this->ask(
            $input,
            $output,
            new SimpleQuestion('<question>Enter your project description:</question>')
        ));
        return true;
    }
}
