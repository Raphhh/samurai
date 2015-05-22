<?php
namespace Samurai\Alias\Task;

use Samurai\Alias\Alias;
use Samurai\Task\ITask;
use Samurai\Task\Task;
use SimilarText\Finder;
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
    {
        if($input->getArgument('name')){
            return $this->listAlias($input->getArgument('name'), $output);
        }
        return $this->listAliases($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    private function listAliases(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('global')) {
            $output->writeln($this->mapAliases($this->getService('alias_manager')->getGlobal()));
        } elseif ($input->getOption('local')) {
            $output->writeln($this->mapAliases($this->getService('alias_manager')->getLocal()));
        } else {
            $output->writeln($this->mapAliases($this->getService('alias_manager')->getAll()));
        }
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param $aliasName
     * @param OutputInterface $output
     * @return int
     */
    private function listAlias($aliasName, OutputInterface $output)
    {
        if(!$this->getService('alias_manager')->has($aliasName)){
            $textFinder = new Finder($aliasName, array_keys($this->getService('alias_manager')->getAll()));
            $output->writeln(sprintf(
                '<error>Alias "%s" not found! Did you mean "%s"?</error>',
                $aliasName,
                $textFinder->first()
            ));
            return ITask::BLOCKING_ERROR_CODE;
        }
        $output->writeln($this->mapAlias($this->getService('alias_manager')->get($aliasName)));
        return ITask::NO_ERROR_CODE;
    }

    /**
     * @param Alias[] $aliases
     * @return string
     */
    private function mapAliases(array $aliases)
    {
        $result = '';
        foreach($aliases as $name => $alias){
            $result .= $this->mapAlias($alias) . "\n\n";
        }
        return trim($result);
    }
    
    /**
     * @param Alias $alias
     * @return string
     */
    private function mapAlias(Alias $alias)
    {
        $result = '';
        foreach ($alias->toArray() as $property => $value) {
            if (is_scalar($value) || is_null($value)) {
                $result .= "$property: $value\n";
            }
        }
        return trim($result);
    }
}
