<?php
namespace Samurai\Task;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PlannerTest
 * @package Samurai\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class PlannerTest extends \PHPUnit_Framework_TestCase
{

    public function testExecuteWithEmptyTasks()
    {
        $planner = new Planner();
        $this->assertTrue(
            $planner->execute($this->provideInput(), $this->provideOutput())
        );
    }

    /**
     *
     */
    public function testExecuteWithTrueTasks()
    {
        $input = $this->provideInput();
        $output = $this->provideOutput();

        $planner = new Planner([
            $this->provideTask(true, true, $input, $output),
            $this->provideTask(true, true, $input, $output),
        ]);

        $this->assertTrue(
            $planner->execute($input, $output)
        );
    }

    /**
     *
     */
    public function testExecuteWithFalseTasks()
    {
        $input = $this->provideInput();
        $output = $this->provideOutput();

        $planner = new Planner([
            $this->provideTask(true, true, $input, $output),
            $this->provideTask(false, true, $input, $output),
            $this->provideTask(false, false, $input, $output),
        ]);

        $this->assertFalse(
            $planner->execute($input, $output)
        );
    }

    /**
     * @return InputInterface
     */
    private function provideInput()
    {
        return new ArrayInput([]);
    }

    /**
     * @return OutputInterface
     */
    private function provideOutput()
    {
        return new NullOutput();
    }

    /**
     * @param $result
     * @param $mustBeCalled
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Samurai\Task\DummyTask
     */
    private function provideTask($result, $mustBeCalled,InputInterface $input, OutputInterface $output)
    {
        $task = $this->getMockBuilder('Samurai\Task\DummyTask')
            ->setConstructorArgs([$result])
            ->setMethods(['execute'])
            ->getMock();

        if($mustBeCalled){
            $task->expects($this->once())
                ->method('execute')
                ->with($input, $output)
                ->will($this->returnValue($result));
        }else{
            $task->expects($this->never())
                ->method('execute');
        }

        return $task;
    }
}
