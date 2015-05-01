<?php
namespace Samurai\Project\Task;

use Samurai\Project\Composer\ComposerConfigMerger;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ComposerConfigSetting
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ComposerConfigSetting extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Initializing composer config</info>');

        $this->resetConfig();

        if(!$this->getService('composer')->validateConfig($this->getService('project')->getDirectoryPath())) {
            $output->writeln('<error>Error: Composer config is not valid</error>');
        }
        if(!$this->getService('composer')->dumpAutoload($this->getService('project')->getDirectoryPath())) {
            $output->writeln('<error>Error: autoload is not up-to-date. Process to "composer dump-autoload".</error>');
        }

        return true;
    }

    /**
     *
     */
    public function resetConfig()
    {
        $merger = new ComposerConfigMerger();
        $this->getService('composer')->setConfig(
            $merger->merge($this->retrieveCurrentConfig(), $this->getService('project')->toConfig()),
            $this->getService('project')->getDirectoryPath()
        );
    }

    /**
     * @return array
     */
    public function retrieveCurrentConfig()
    {
        return $this->getService('composer')->getConfig($this->getService('project')->getDirectoryPath());
    }
}
