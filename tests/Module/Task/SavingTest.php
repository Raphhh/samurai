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
 * Class SavingTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class SavingTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildModule()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'package' => 'package',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Saving(
            $this->provideServices(
                $this->provideModule($args),
                false,
                true,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
    }

    public function testBuildModuleWithoutOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'package' => 'package',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Saving(
            $this->provideServices(
                $this->provideModule($args),
                true,
                false,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
    }

    public function testBuildModuleWithOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'package' => 'package',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Saving(
            $this->provideServices(
                $this->provideModule($args),
                true,
                true,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
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
                new InputArgument('package'),
                new InputArgument('version'),
                new InputArgument('source'),
            ])
        );
    }

    /**
     * @param Module $module
     * @param bool $hasAlreadyModule
     * @param bool $willBeSaved
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Container
     */
    private function provideServices(Module $module, $hasAlreadyModule, $willBeSaved, InputInterface $input, OutputInterface $output)
    {
        $services = new Container();

        $moduleManager = $this->provideModuleManager($module, $hasAlreadyModule);
        $services['module_manager'] = function () use($moduleManager){
            return $moduleManager;
        };

        if($hasAlreadyModule) {
            $questionHelper = $this->provideQuestionHelper($input, $output, $willBeSaved);
            $services['helper_set'] = function () use ($questionHelper) {
                return new HelperSet(['question' => $questionHelper]);
            };
        }

        $moduleProcedure = $this->provideModuleProcedure($willBeSaved);
        $services['module_procedure'] = function () use($moduleProcedure){
            return $moduleProcedure;
        };

        return $services;
    }

    private function provideModule(array $args)
    {
        $module = new Module();
        $module->setName($args['name']);
        $module->setDescription($args['description']);
        $module->setPackage($args['package']);
        $module->setVersion($args['version']);
        $module->setSource($args['source']);
        return $module;
    }

    /**
     * @param Module $newModule
     * @param $hasModule
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideModuleManager(Module $newModule, $hasModule)
    {
        $oldModule = clone $newModule;
        $oldModule->setDescription($newModule->getDescription() . '_old');
        $oldModule->setPackage($newModule->getPackage() . '_old');
        $oldModule->setVersion($newModule->getVersion() . '_old');

        $moduleManager = $this->getMockBuilder('Balloon\Balloon')
            ->disableOriginalConstructor()
            ->getMock();

        $moduleManager->expects($this->once())
            ->method('has')
            ->with($newModule->getName())
            ->will($this->returnValue($hasModule));

        $moduleManager->expects($this->any())
            ->method('get')
            ->with($newModule->getName())
            ->will($this->returnValue($oldModule));

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
                        return '<question>Do you want to override the module "package_old version_old" with "package version"?</question>[y]' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue($result));

        return $questionHelper;
    }

    /**
     * @param bool $isCalled
     * @return \Samurai\Module\ModuleProcedure
     */
    private function provideModuleProcedure($isCalled)
    {
        $moduleProcedure = $this->getMockBuilder('Samurai\Module\ModuleProcedure')->disableOriginalConstructor()->getMock();

        $moduleProcedure->expects($this->exactly((int)$isCalled))
            ->method('import')
            ->will($this->returnValue(true));

        return $moduleProcedure;
    }
}
