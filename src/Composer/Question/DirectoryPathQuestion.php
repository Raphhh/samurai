<?php
namespace Samurai\Composer\Question;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BootstrapQuestion
 * @package Samurai\Composer\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DirectoryPathQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $optionValue = $input->getOption('dir');
        if($optionValue){
            $this->getProject()->setDirectoryPath($optionValue);
        }else{
            $this->getProject()->setDirectoryPath($this->getProject()->getName());
        }
        return (bool)$this->getProject()->getDirectoryPath();
    }
}
