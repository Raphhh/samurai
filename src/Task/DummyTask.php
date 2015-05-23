<?php
namespace Samurai\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DummyTask
 * @package Samurai\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DummyTask implements ITask
{
    /**
     * @var int|null
     */
    private $result;

    /**
     * @param int|null $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->result;
    }
}
