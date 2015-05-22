<?php
namespace Samurai\Alias\Task;

use Samurai\Alias\Alias;
use Samurai\Task\ITask;
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
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {//todo align on module
        if($input->getOption('global')){
            $output->writeln($this->mapAlias($this->getService('alias_manager')->getGlobal()));
        }elseif($input->getOption('local')) {
            $output->writeln($this->mapAlias($this->getService('alias_manager')->getLocal()));
        }else{
            $output->writeln($this->mapAlias($this->getService('alias_manager')->getAll()));
        }
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param Alias[] $aliasList
     * @return string
     */
    private function mapAlias(array $aliasList)
    {
        $result = '';
        foreach($aliasList as $name => $alias){
            foreach($alias->toArray() as $property => $value){
                $result .= "$property: $value\n";
            }
            $result .= "\n";
        }
        return trim($result);
    }
}
