<?php
namespace Samurai\Module\Task;

use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ListingTest
 * @package Samurai\Alias\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ListingTest extends \PHPUnit_Framework_TestCase
{

    public function testExecuteAll()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $saving = new Listing($this->provideServicesForAll($this->provideModule($args)));
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("1 module(s) set:\n\nname: name\ndescription: description\npackage: bootstrap\nversion: version\nsource: source\nisEnable: 1\n", $output->fetch());
    }

    public function testExecuteOne()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $saving = new Listing($this->provideServicesForOne($this->provideModule($args)));
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\npackage: bootstrap\nversion: version\nsource: source\nisEnable: 1\n", $output->fetch());
    }

    public function testExecuteNotExisting()
    {
        $input = $this->provideInput(['name' => 'my-module']);
        $output = new BufferedOutput();

        $saving = new Listing($this->provideServicesForNotExisting());
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("Module \"my-module\" not found!\n", $output->fetch());
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
     * @param array $args
     * @return Module
     */
    private function provideModule(array $args)
    {
        $alias = new Module();
        $alias->setName($args['name']);
        $alias->setDescription($args['description']);
        $alias->setPackage($args['bootstrap']);
        $alias->setVersion($args['version']);
        $alias->setSource($args['source']);
        $alias->setIsEnable(true);
        return $alias;
    }

    /**
     * @param Module $module
     * @return Container
     */
    private function provideServicesForAll(Module $module)
    {
        $services = new Container();

        $aliasManager = $this->provideModuleManagerForAll($module);
        $services['module_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        return $services;
    }

    /**
     * @param Module $module
     * @return \Samurai\Module\ModuleManager
     */
    private function provideModuleManagerForAll(Module $module)
    {
        $aliasManager = $this->getMockBuilder('Samurai\Module\ModuleManager')
            ->disableOriginalConstructor()
            ->getMock();

        $aliasManager->expects($this->atLeastOnce())
            ->method('getAll')
            ->will($this->returnValue(new Modules([$module->getName() => $module])));

        return $aliasManager;
    }

    /**
     * @param Module $module
     * @return Container
     */
    private function provideServicesForOne(Module $module)
    {
        $services = new Container();

        $aliasManager = $this->provideModuleManagerForOne($module);
        $services['module_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        return $services;
    }

    /**
     * @param Module $module
     * @return \Samurai\Module\ModuleManager
     */
    private function provideModuleManagerForOne(Module $module)
    {
        $aliasManager = $this->getMockBuilder('Samurai\Module\ModuleManager')
            ->disableOriginalConstructor()
            ->getMock();

        $aliasManager->expects($this->once())
            ->method('has')
            ->with($module->getName())
            ->will($this->returnValue(true));

        $aliasManager->expects($this->once())
            ->method('get')
            ->with($module->getName())
            ->will($this->returnValue($module));

        return $aliasManager;
    }

    /**
     * @return Container
     */
    private function provideServicesForNotExisting()
    {
        $services = new Container();

        $aliasManager = $this->provideModuleManagerForNotExisting();
        $services['module_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        return $services;
    }

    /**
     * @return \Samurai\Module\ModuleManager
     */
    private function provideModuleManagerForNotExisting()
    {
        $aliasManager = $this->getMockBuilder('Samurai\Module\ModuleManager')
            ->disableOriginalConstructor()
            ->getMock();

        $aliasManager->expects($this->once())
            ->method('has')
            ->with('my-module')
            ->will($this->returnValue(false));

        $aliasManager->expects($this->never())
            ->method('get');

        return $aliasManager;
    }
}
