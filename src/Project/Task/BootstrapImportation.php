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
     * @var array
     */
    private $optionsMapping = [
        'url' => 'repository-url',//todo use alias::source instead. this options is not allowed anymore
    ];

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            '<info>Installing project %s from %s</info>',
            $this->getService('project')->getName(),
            $this->getService('project')->getBootstrapName()
        ));

        return $this->getService('composer')->createProject($this->filter($input->getOptions()));
    }

    /**
     * @param array $options
     * @return array
     */
    private function filter(array $options)
    {
        $result = [];
        foreach($this->optionsMapping as $alias => $option){
            if(!empty($options[$alias])){
                $result[$option] = $options[$alias];
            }
        }
        return $result;
    }
}
