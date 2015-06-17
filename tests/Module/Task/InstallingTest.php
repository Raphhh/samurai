<?php
namespace Samurai\Module\Task;

use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Task\ITask;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallingTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class InstallingTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithModule()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $installing = new Installing(
            $this->provideServices(
                true,
                true,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $installing->execute($input, $output));
        $this->assertSame(
            "Stating modules installation\nModule \"raphhh/samurai-module-git\" already installed\nModule \"raphhh/samurai-module-cleaner\" already installed\n",
            $output->fetch()
        );
    }

    public function testExecuteWithoutModuleButNotInstalled()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $installing = new Installing(
            $this->provideServices(
                false,
                false,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $installing->execute($input, $output));
        $this->assertSame("Stating modules installation\n", $output->fetch());
    }

    public function testExecuteWithoutModule()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();

        $installing = new Installing(
            $this->provideServices(
                false,
                true,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $installing->execute($input, $output));
        $this->assertSame("Stating modules installation\n", $output->fetch());
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
     * @param bool $hasModule
     * @param $willBeInstalled
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Container
     */
    private function provideServices($hasModule, $willBeInstalled, InputInterface $input, OutputInterface $output)
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManager($hasModule);
        $services['module_manager'] = function () use($moduleManager){
            return $moduleManager;
        };

        $moduleProcedure = $this->provideModuleProcedure($hasModule, $willBeInstalled);
        $services['module_procedure'] = function () use($moduleProcedure){
            return $moduleProcedure;
        };

        if(!$hasModule) {
            $questionHelper = $this->provideQuestionHelper($input, $output, $willBeInstalled);
            $services['helper_set'] = function () use ($questionHelper) {
                return new HelperSet(['question' => $questionHelper]);
            };
        }

        return $services;
    }

    /**
     * @param $hasModule
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleManager($hasModule)
    {
        $modules = new Modules();
        if($hasModule){
            $modules[] = new Module();
        }

        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')
            ->disableOriginalConstructor()
            ->getMock();

        $moduleManager->expects($this->atLeastOnce())
            ->method('getByPackage')
            ->will($this->returnValue($modules));

        return $moduleManager;
    }

    /**
     * @param $hasModule
     * @param $willBeInstalled
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleProcedure($hasModule, $willBeInstalled)
    {
        $moduleProcedure = $this->getMockBuilder('Samurai\Module\ModuleProcedure')->disableOriginalConstructor()->getMock();

        if(!$hasModule && $willBeInstalled){
            $moduleProcedure->expects($this->atLeastOnce())
                ->method('import')
                ->will($this->returnValue(true));
        }else{
            $moduleProcedure->expects($this->never())
                ->method('import');
        }

        return $moduleProcedure;
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

        $questionHelper->expects($this->atLeastOnce())
            ->method('ask')
            ->with(
                $input,
                $output/*,
                $this->callback(
                    function (ConfirmationQuestion $question) {
                        return '<question>Do you want to install the module "name"</question>[y]' === $question->getQuestion();
                    }
                )*/
            )
            ->will($this->returnValue($result));

        return $questionHelper;
    }
}
