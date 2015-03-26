<?php
namespace Samurai\Project\Task;

use Pimple\Container;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ConfigSettingTest
 * @package Samurai\Project\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ConfigSettingTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithValidConfig()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices(true);

        $task = new ConfigSetting($services);
        $this->assertTrue($task->execute($input, $output));

        $this->assertSame("Initializing composer config\n", $output->fetch());
    }

    public function testExecuteWithNotValidConfig()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices(false);

        $task = new ConfigSetting($services);
        $this->assertTrue($task->execute($input, $output));

        $this->assertSame("Initializing composer config\nError: Composer config is not valid\n", $output->fetch());
    }

    /**
     * @param $result
     * @return Container
     */
    private function provideServices($result)
    {
        $composer = $this->provideComposer($result);

        $services = new Container();
        $services['composer'] = function () use ($composer) {
            return $composer;
        };
        return $services;
    }

    private function provideComposer($result)
    {
        $composer = $this->getMockBuilder('Samurai\Project\Composer')
            ->disableOriginalConstructor()
            ->getMock();

        $composer->expects($this->once())
            ->method('resetConfig');

        $composer->expects($this->once())
            ->method('validateConfig')
            ->will($this->returnValue($result));

        return $composer;
    }
}