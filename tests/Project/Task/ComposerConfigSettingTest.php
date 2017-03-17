<?php
namespace Samurai\Project\Task;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Project\Project;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ComposerConfigSettingTest
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ComposerConfigSettingTest extends TestCase
{
    public function testExecuteWithValidConfig()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices(0);

        $task = new ComposerConfigSetting($services);
        $this->assertSame(ITask::NON_BLOCKING_ERROR_CODE, $task->execute($input, $output));

        $this->assertSame("Initializing composer config\n", $output->fetch());
    }

    public function testExecuteWithNotValidConfig()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices(1);

        $task = new ComposerConfigSetting($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));

        $this->assertSame(
            "Initializing composer config\nError: Composer config is not valid\nError: autoload is not up-to-date. Process to \"composer dump-autoload\".\n",
            $output->fetch()
        );
    }

    /**
     * @param $result
     * @return Container
     */
    private function provideServices($result)
    {
        $composer = $this->provideComposer($result);

        $services = new Container();
        $services['project'] = function () use ($composer) {
            return new Project();
        };

        $services['composer'] = function () use ($composer) {
            return $composer;
        };
        return $services;
    }

    private function provideComposer($result)
    {
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')
            ->disableOriginalConstructor()
            ->getMock();

        $composer->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue([]));

        $composer->expects($this->once())
            ->method('setConfig');

        $composer->expects($this->once())
            ->method('validateConfig')
            ->will($this->returnValue($result));

        $composer->expects($this->once())
            ->method('flushConfig')
            ->will($this->returnValue($result));

        $composer->expects($this->once())
            ->method('dumpAutoload')
            ->will($this->returnValue($result));

        return $composer;
    }
}
