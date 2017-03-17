<?php
namespace Samurai\Module\Task;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class EnablingTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class EnablingTest extends TestCase
{

    public function testExecuteEnable()
    {
        $input = $this->provideInput(['name' => 'module-name', 'action' => 'enable']);
        $output = new BufferedOutput();

        $module = new Module();
        $module->setName('module-name');
        $module->setIsEnable(false);

        $task = new Enabling($this->provideServices($module, true));
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));
        $this->assertSame("Module \"module-name\" modified\n", $output->fetch());
        $this->assertTrue($module->isEnable());
    }

    public function testExecuteDisable()
    {
        $input = $this->provideInput(['name' => 'module-name', 'action' => 'disable']);
        $output = new BufferedOutput();

        $module = new Module();
        $module->setName('module-name');
        $module->setIsEnable(true);

        $task = new Enabling($this->provideServices($module, true));
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));
        $this->assertSame("Module \"module-name\" modified\n", $output->fetch());
        $this->assertFalse($module->isEnable());
    }


    public function testExecuteWithoutModule()
    {
        $input = $this->provideInput(['name' => 'module-name', 'action' => 'enable']);
        $output = new BufferedOutput();

        $module = new Module();
        $module->setName('module-name');
        $module->setIsEnable(false);

        $task = new Enabling($this->provideServices($module, false));
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $task->execute($input, $output));
        $this->assertSame("Module \"module-name\" not found! Did you mean \"other\"?\n", $output->fetch());
        $this->assertFalse($module->isEnable());
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
                new InputArgument('name'),
                new InputArgument('action'),
            ])
        );
    }

    private function provideServices(Module $module, $hasModule)
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManager($module, $hasModule);
        $services['module_manager'] = function() use($moduleManager){
            return $moduleManager;
        };

        return $services;
    }

    private function provideModuleManager(Module $module, $hasModule)
    {
        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(new Modules(['other' => $module])));

        $moduleManager->expects($this->once())
            ->method('has')
            ->with($module->getName())
            ->will($this->returnValue($hasModule));

        if($hasModule) {
            $moduleManager->expects($this->once())
                ->method('get')
                ->with($module->getName())
                ->will($this->returnValue($module));

            $moduleManager->expects($this->once())
                ->method('modify')
                ->with($module->getName(), $module);

        }else{
            $moduleManager->expects($this->never())
                ->method('get');
        }

        return $moduleManager;
    }
}
