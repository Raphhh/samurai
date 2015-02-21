<?php
namespace Samurai\Composer\Question;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BootstrapQuestion
 * @package Samurai\Composer\Question
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
        $this->getProject()->setBootstrapName($input->getArgument('bootstrap'));
        $this->getProject()->setBootstrapVersion($input->getArgument('version'));
        return (bool)$this->getProject()->getBootstrapName();
    }
}
