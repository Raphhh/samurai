<?php
namespace Samurai\Task;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PlannerTest
 * @package Samurai\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class PlannerTest extends TestCase
{

    public function testExecuteWithEmptyTasks()
    {
        $planner = new Planner();
        $this->assertSame(
            ITask::NO_ERROR_CODE,
            $planner->execute($this->provideInput(), $this->provideOutput())
        );
    }

    /**
     *
     */
    public function testExecuteWithNoErrorTasks()
    {
        $input = $this->provideInput();
        $output = $this->provideOutput();

        $planner = new Planner([
            $this->provideTask(null, true, $input, $output),
            $this->provideTask(ITask::NO_ERROR_CODE, true, $input, $output),
        ]);

        $this->assertSame(
            ITask::NO_ERROR_CODE,
            $planner->execute($input, $output)
        );
    }

    /**
     *
     */
    public function testExecuteWithBlockingTasks()
    {
        $input = $this->provideInput();
        $output = $this->provideOutput();

        $planner = new Planner([
            $this->provideTask(ITask::NO_ERROR_CODE, true, $input, $output),
            $this->provideTask(ITask::NON_BLOCKING_ERROR_CODE, true, $input, $output),
            $this->provideTask(ITask::BLOCKING_ERROR_CODE, true, $input, $output),
            $this->provideTask(ITask::BLOCKING_ERROR_CODE, false, $input, $output),
        ]);

        $this->assertSame(
            ITask::NO_ERROR_CODE | ITask::NON_BLOCKING_ERROR_CODE | ITask::BLOCKING_ERROR_CODE,
            $planner->execute($input, $output)
        );
    }

    /**
     *
     */
    public function testExecuteWithNonBlockingTasks()
    {
        $input = $this->provideInput();
        $output = $this->provideOutput();

        $planner = new Planner([
            $this->provideTask(ITask::NO_ERROR_CODE, true, $input, $output),
            $this->provideTask(ITask::NON_BLOCKING_ERROR_CODE, true, $input, $output),
            $this->provideTask(ITask::NON_BLOCKING_ERROR_CODE, true, $input, $output),
        ]);

        $this->assertSame(
            ITask::NO_ERROR_CODE | ITask::NON_BLOCKING_ERROR_CODE,
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
