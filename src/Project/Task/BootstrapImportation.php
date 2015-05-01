<?php
namespace Samurai\Project\Task;

use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BootstrapImportation
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class BootstrapImportation extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$this->getService('project')->getBootstrap()){
           throw new \InvalidArgumentException('The bootstrap of the project is not defined');
        }

        $output->writeln(sprintf(
            '<info>Installing project %s from %s</info>',
            $this->getService('project')->getName(),
            $this->getService('project')->getBootstrap()->getPackage()
        ));

        return $this->getService('composer')->createProject($this->getService('project'), $this->getOptions());
    }

    /**
     * @return array
     */
    private function getOptions()
    {
        return array_filter([
            'repository-url' => $this->getService('project')->getBootstrap()->getSource()
        ]);
    }
}
