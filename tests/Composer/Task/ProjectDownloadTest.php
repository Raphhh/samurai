<?php
namespace Samurai\Composer\Task;

use Pimple\Container;
use Samurai\Composer\Composer;
use Samurai\Composer\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ProjectDownloadTest
 * @package Samurai\Composer\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ProjectDownloadTest extends \PHPUnit_Framework_TestCase
{

    public function testExecuteWithoutBootstrap()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices($this->provideExecutor(0));

        $task = new ProjectDownload($services);
        $this->setExpectedException('InvalidArgumentException', 'The bootstrap of the project is not defined');
        $this->assertFalse($task->execute($input, $output));
    }

    public function testExecute()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices($this->provideExecutor(1));
        $services['composer']->getProject()->setName('vendor/package');
        $services['composer']->getProject()->setBootstrapName('vendor/bootstrap');

        $task = new ProjectDownload($services);
        $this->assertTrue($task->execute($input, $output));

        $this->assertSame("Installing project vendor/package from vendor/bootstrap\n", $output->fetch());
    }

    public function testExecuteWithOptions()
    {
        $input = $this->provideInput(['--url' => 'specific/url']);
        $output = new BufferedOutput();

        $services = $this->provideServices($this->provideExecutor(1, ' --repository-url=specific/url'));
        $services['composer']->getProject()->setName('vendor/package');
        $services['composer']->getProject()->setBootstrapName('vendor/bootstrap');

        $task = new ProjectDownload($services);
        $this->assertTrue($task->execute($input, $output));

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
        $services['composer'] = function () use ($executor) {
            return new Composer(new Project(), $executor);
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
