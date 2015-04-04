<?php
namespace Samurai\Project\Task;

use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigSetting
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ConfigSetting extends Task
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Initializing composer config</info>');

        $this->getService('composer')->resetConfig();
        if(!$this->getService('composer')->validateConfig()) {
            $output->writeln('<error>Error: Composer config is not valid</error>');
        }
        if(!$this->getService('composer')->dumpAutoload()) {
            $output->writeln('<error>Error: autoload is not up-to-date. Process to "composer dump-autoload".</error>');
        }

        return true;
    }
}
