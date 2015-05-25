<?php
namespace Samurai\Module\Task;

use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Task\ITask;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class RemovingTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RemovingTest extends \PHPUnit_Framework_TestCase
{

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

        $removing = new Removing(
            $this->provideServices(
                $this->provideModule($args),
                false,
                false,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $removing->execute($input, $output));
        $this->assertSame("Error: no module \"name\" found\n", $output->fetch());
    }

    public function testExecuteWithModuleAndOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $removing = new Removing(
            $this->provideServices(
                $this->provideModule($args),
                true,
                true,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $removing->execute($input, $output));
        $this->assertSame("", $output->fetch());
    }

    public function testExecuteWithModuleButNoOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $removing = new Removing(
            $this->provideServices(
                $this->provideModule($args),
                true,
                false,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $removing->execute($input, $output));
        $this->assertSame("", $output->fetch());
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
                new InputArgument('description'),
                new InputArgument('bootstrap'),
                new InputArgument('version'),
            ])
        );
    }

    /**
     * @param Module $module
     * @param bool $hasModule
     * @param bool $willBeRemoved
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Container
     */
    private function provideServices(Module $module, $hasModule, $willBeRemoved, InputInterface $input, OutputInterface $output)
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManager($module, $hasModule, $willBeRemoved);
        $services['module_manager'] = function () use($moduleManager){
            return $moduleManager;
        };

        $moduleProcedure = $this->provideModuleProcedure($module, $hasModule, $willBeRemoved);
        $services['module_procedure'] = function () use($moduleProcedure){
            return $moduleProcedure;
        };

        if($hasModule) {
            $questionHelper = $this->provideQuestionHelper($input, $output, $willBeRemoved);
            $services['helper_set'] = function () use ($questionHelper) {
                return new HelperSet(['question' => $questionHelper]);
            };
        }

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
     * @param $willBeRemoved
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleManager(Module $module, $hasModule, $willBeRemoved)
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param bool $result
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    private function provideQuestionHelper(InputInterface $input, OutputInterface $output, $result)
    {
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->callback(
                    function (ConfirmationQuestion $question) {
                        return '<question>Do you want to remove the module "bootstrap"</question>[y]' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue($result));

        return $questionHelper;
    }

    /**
     * @param Module $module
     * @param $hasModule
     * @param $willBeRemoved
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleProcedure(Module $module, $hasModule, $willBeRemoved)
    {
        $moduleProcedure = $this->getMockBuilder('Samurai\Module\ModuleProcedure')->disableOriginalConstructor()->getMock();

        if($hasModule && $willBeRemoved){
            $moduleProcedure->expects($this->once())
                ->method('remove')
                ->with($module)
                ->will($this->returnValue(true));
        }else{
            $moduleProcedure->expects($this->never())
                ->method('remove');
        }

        return $moduleProcedure;
    }
}
