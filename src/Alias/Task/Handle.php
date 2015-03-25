<?php
namespace Samurai\Alias\Task;

use Samurai\Alias\Task\Factory\AliasManagementTaskFactory;
use Samurai\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Handle
 * @package Samurai\Alias\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Handle extends Task
{
    /**
     * @var AliasManagementTaskFactory
     */
    private $taskFactory;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    { //todo remove this class. no utilities.
        return $this->getTaskFactory()->create($input, $this->getServices())->execute($input, $output);
    }

    /**
     * Getter of $taskFactory
     *
     * @return AliasManagementTaskFactory
     */
    public function getTaskFactory()
    {
        return $this->taskFactory ? : new AliasManagementTaskFactory();
    }

    /**
     * Setter of $taskFactory
     *
     * @param AliasManagementTaskFactory $taskFactory
     */
    public function setTaskFactory(AliasManagementTaskFactory $taskFactory)
    {
        $this->taskFactory = $taskFactory;
    }


}
