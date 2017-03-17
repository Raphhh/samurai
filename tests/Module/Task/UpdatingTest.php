<?php
namespace Samurai\Module\Task;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class UpdatingTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class UpdatingTest extends TestCase
{
    public function testExecuteWithModule()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $updating = new Updating(
            $this->provideServices(
                $this->provideModule($args),
                true
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $updating->execute($input, $output));
        $this->assertSame("", $output->fetch());
    }

    public function testExecuteWithoutModule()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $updating = new Updating(
            $this->provideServices(
                $this->provideModule($args),
                false
            )
        );
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $updating->execute($input, $output));
        $this->assertSame("Error: no module \"name\" found\n", $output->fetch());
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
            ])
        );
    }

    /**
     * @param Module $module
     * @param bool $hasModule

     * @return Container
     */
    private function provideServices(Module $module, $hasModule)
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManager($module, $hasModule);
        $services['module_manager'] = function () use($moduleManager){
            return $moduleManager;
        };

        $moduleProcedure = $this->provideModuleProcedure($module, $hasModule);
        $services['module_procedure'] = function () use($moduleProcedure){
            return $moduleProcedure;
        };


        return $services;
    }

    /**
     * @param array $args
     * @return Module
     */
    private function provideModule(array $args)
    {
        $module = new Module();
        $module->setName($args['name']);
        $module->setDescription($args['description']);
        $module->setPackage($args['bootstrap']);
        $module->setVersion($args['version']);
        return $module;
    }

    /**
     * @param Module $module
     * @param $hasModule
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleManager(Module $module, $hasModule)
    {
        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')
            ->disableOriginalConstructor()
            ->getMock();

        $moduleManager->expects($this->once())
            ->method('has')
            ->with($module->getName())
            ->will($this->returnValue($hasModule));

        if($hasModule) {
            $moduleManager->expects($this->once())
                ->method('get')
                ->will($this->returnValue($module));
        }else{
            $moduleManager->expects($this->never())
                ->method('get');
        }

        return $moduleManager;
    }

    /**
     * @param Module $module
     * @param $hasModule
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleProcedure(Module $module, $hasModule)
    {
        $moduleProcedure = $this->getMockBuilder('Samurai\Module\ModuleProcedure')->disableOriginalConstructor()->getMock();

        if($hasModule){
            $moduleProcedure->expects($this->once())
                ->method('update')
                ->with($module)
                ->will($this->returnValue(true));
        }else{
            $moduleProcedure->expects($this->never())
                ->method('update');
        }

        return $moduleProcedure;
    }
}
