<?php
namespace Samurai\Module\Task;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Task\ITask;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class RunningTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RunningTest extends TestCase
{

    public function testExecuteForAll()
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManagerForAll();
        $services['module_manager'] = function() use($moduleManager){
            return $moduleManager;
        };

        $questionHelper = $this->getQuestionHelper();
        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $task = new Running($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));

        $this->assertSame("Running 2 module(s)\nABB", $output->fetch());
    }

    public function testExecuteForOne()
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManagerForOne();
        $services['module_manager'] = function() use($moduleManager){
            return $moduleManager;
        };

        $questionHelper = $this->getQuestionHelper();
        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        $input = $this->provideInput(['name' => 'moduleA']);
        $output = new BufferedOutput();

        $task = new Running($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $task->execute($input, $output));

        $this->assertSame("Running the module \"name of A\"\nAB", $output->fetch());
    }

    /**
     * @return \Samurai\Module\ModuleManager
     */
    private function provideModuleManagerForAll()
    {

        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();

        $moduleManager->expects($this->once())
            ->method('getAll')
            ->will($this->returnValue($this->providesModules()));

        return $moduleManager;
    }

    /**
     * @return \Samurai\Module\ModuleManager
     */
    private function provideModuleManagerForOne()
    {

        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();

        $moduleManager->expects($this->once())
            ->method('has')
            ->with('moduleA')
            ->will($this->returnValue(true));

        $moduleManager->expects($this->once())
            ->method('get')
            ->with('moduleA')
            ->will($this->returnValue($this->providesModules()[0]));

        return $moduleManager;
    }

    /**
     * @return Modules
     */
    private function providesModules()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setName('name of A');
        $moduleA->setIsEnable(true);
        $moduleA->setTasks([
            'Samurai\Module\resources\TaskA',
            'Samurai\Module\resources\TaskB',
        ]);
        $modules[] = $moduleA;

        $moduleB = new Module();
        $moduleB->setName('name of B');
        $moduleB->setIsEnable(true);
        $moduleB->setTasks([
            'Samurai\Module\resources\TaskB',
        ]);
        $modules[] = $moduleB;

        $moduleC = new Module();
        $moduleC->setName('name of C');
        $moduleC->setTasks([
            'Samurai\Module\resources\TaskC',
        ]);
        $modules[] = $moduleC;

        return $modules;
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

    private function getQuestionHelper()
    {
        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $questionHelper->expects($this->any())
            ->method('ask')
            ->will($this->returnValue(true));
        return $questionHelper;
    }
}
