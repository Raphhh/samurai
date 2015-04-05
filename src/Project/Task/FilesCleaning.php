<?php
namespace Samurai\Project\Task;

use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FilesCleaning
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class FilesCleaning extends Task
{

    /**
     * @var array
     */
    private static $patterns = [
        'CHANGELOG*',
        'UPGRADE*',
    ];

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Cleaning files</info>');
        $this->remove($output);
    }

    /**
     * @param OutputInterface $output
     */
    private function remove(OutputInterface $output)
    {
        $projectDir = $this->getService('composer')->getProject()->getDirectoryPath();
        foreach (self::$patterns as $pattern) {
            $this->removeByPattern($output, $projectDir . DIRECTORY_SEPARATOR . $pattern);
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $pattern
     */
    private function removeByPattern(OutputInterface $output, $pattern)
    {
        foreach (glob($pattern) as $filename) {
            $output->writeln('Removing file '.$filename);
            if(!unlink($filename)){
                $output->writeln('<error>Error: file '.$filename.' not deleted!</error>');
            }
        }
    }
}
