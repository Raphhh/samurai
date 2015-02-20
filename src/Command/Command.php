<?php
namespace Samurai\Command;

use Samurai\Task\ITask;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Class Command
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Command extends SymfonyCommand
{
    /**
     * @var ITask
     */
    private $task;

    /**
     * @param ITask $task
     * @param string $name
     */
    public function __construct(ITask $task, $name = null)
    {
        $this->setTask($task);
        parent::__construct($name);
    }

    /**
     * Getter of $task
     *
     * @return ITask
     */
    protected function getTask()
    {
        return $this->task;
    }

    /**
     * Setter of $task
     *
     * @param ITask $task
     */
    private function setTask(ITask $task)
    {
        $this->task = $task;
    }
}
