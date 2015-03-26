<?php
namespace Samurai\Alias\Task;

use Samurai\Alias\Alias;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Listing
 * @package Samurai\Alias\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Listing extends Task
{

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($input->getOption('global')){
            $output->writeln($this->mapAlias($this->getService('alias_manager')->getGlobal()));
        }elseif($input->getOption('local')) {
            $output->writeln($this->mapAlias($this->getService('alias_manager')->getLocal()));
        }else{
            $output->writeln($this->mapAlias($this->getService('alias_manager')->getAll()));
        }
        return true;
    }

    /**
     * @param Alias[] $aliasList
     * @return string
     */
    private function mapAlias(array $aliasList)
    {
        $result = '';
        foreach($aliasList as $name => $alias){
            $result .= '[' . $name . '] ' . $alias . "\n";
        }
        return trim($result);
    }
}
