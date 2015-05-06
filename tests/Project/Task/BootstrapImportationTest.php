<?php
namespace Samurai\Project\Task;

use Balloon\Factory\BalloonFactory;
use Balloon\Reader\Factory\DummyFileReaderFactory;
use Pimple\Container;
use Samurai\Alias\Alias;
use Samurai\Project\Composer\Composer;
use Samurai\Project\Project;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class BootstrapImportationTest
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class BootstrapImportationTest extends \PHPUnit_Framework_TestCase
{

    public function testExecuteWithoutBootstrap()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices($this->provideExecutor(0));

        $task = new BootstrapImportation($services);
        $this->setExpectedException('InvalidArgumentException', 'The bootstrap of the project is not defined');
        $this->assertFalse($task->execute($input, $output));
    }

    public function testExecute()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $alias = new Alias();
        $alias->setPackage('vendor/bootstrap');
        
        $services = $this->provideServices($this->provideExecutor(1));
        $services['project']->setName('vendor/package');
        $services['project']->setBootstrap($alias);

        $task = new BootstrapImportation($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));

        $this->assertSame("Installing project vendor/package from vendor/bootstrap\n", $output->fetch());
    }

    public function testExecuteWithOptions()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $alias = new Alias();
        $alias->setPackage('vendor/bootstrap');
        $alias->setSource('specific/url');

        $services = $this->provideServices($this->provideExecutor(1, ' --repository-url=specific/url'));
        $services['project']->setName('vendor/package');
        $services['project']->setBootstrap($alias);

        $task = new BootstrapImportation($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));

        $this->assertSame("Installing project vendor/package from vendor/bootstrap\n", $output->fetch());
    }

    /**
     * @param int $callNumber
     * @param string $options
     * @return \TRex\Cli\Executor
     */
    private function provideExecutor($callNumber, $options = '')
    {
        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();

        $executor->expects($this->exactly($callNumber))
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/bootstrap'.$options)
            ->will($this->returnValue(true));

        return $executor;
    }

    /**
     * @param $executor
     * @return Container
     */
    private function provideServices($executor)
    {
        $services = new Container();
        $services['project'] = function () {
            return new Project();
        };
        $services['composer'] = function () use ($executor) {
            return new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        };
        return $services;
    }

    /**
     * @param array $args
     * @return ArrayInput
     */
    private function provideInput(array $args)
    {
        return new ArrayInput(
            $args,
            new InputDefinition([
                new InputOption('url')
            ])
        );
    }
}
