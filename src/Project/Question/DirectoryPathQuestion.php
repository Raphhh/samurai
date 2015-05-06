<?php
namespace Samurai\Project\Question;

use Samurai\Task\ITask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BootstrapQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DirectoryPathQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $optionValue = $input->getOption('dir');
        if($optionValue){
            $this->getProject()->setDirectoryPath($optionValue);
        }else{
            $this->getProject()->setDirectoryPath($this->getProject()->getName());
        }
        return $this->getProject()->getDirectoryPath() ? ITask::NO_ERROR_CODE : ITask::BLOCKING_ERROR_CODE;
    }
}
