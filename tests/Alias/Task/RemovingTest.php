<?php
namespace Samurai\Alias\Task;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Alias\Alias;
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
 * @package Samurai\Alias\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class RemovingTest extends TestCase
{

    public function testExecuteWithoutAlias()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $saving = new Removing(
            $this->provideServices(
                $this->provideAlias($args),
                false,
                false,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("Error: no alias \"name\" found\n", $output->fetch());
    }

    public function testExecuteWithAliasAndOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $saving = new Removing(
            $this->provideServices(
                $this->provideAlias($args),
                true,
                true,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("", $output->fetch());
    }

    public function testExecuteWithAliasButNoOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput(['name' => 'name']);
        $output = new BufferedOutput();

        $saving = new Removing(
            $this->provideServices(
                $this->provideAlias($args),
                true,
                false,
                $input,
                $output
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
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
            ])
        );
    }

    /**
     * @param Alias $alias
     * @param bool $hasAlias
     * @param bool $willBeRemoved
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Container
     */
    private function provideServices(Alias $alias, $hasAlias, $willBeRemoved, InputInterface $input, OutputInterface $output)
    {
        $services = new Container();

        $aliasManager = $this->provideAliasManager($alias, $hasAlias, $willBeRemoved);
        $services['alias_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        if($hasAlias) {
            $questionHelper = $this->provideQuestionHelper($input, $output, $willBeRemoved);
            $services['helper_set'] = function () use ($questionHelper) {
                return new HelperSet(['question' => $questionHelper]);
            };
        }

        return $services;
    }

    /**
     * @param array $args
     * @return Alias
     */
    private function provideAlias(array $args)
    {
        $alias = new Alias();
        $alias->setName($args['name']);
        $alias->setDescription($args['description']);
        $alias->setPackage($args['bootstrap']);
        $alias->setVersion($args['version']);
        return $alias;
    }

    /**
     * @param Alias $alias
     * @param $hasAlias
     * @param bool $willBeRemoved
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideAliasManager(Alias $alias, $hasAlias, $willBeRemoved)
    {
        $aliasManager = $this->getMockBuilder('Samurai\Alias\AliasManager')
            ->disableOriginalConstructor()
            ->getMock();

        $aliasManager->expects($this->once())
            ->method('hasLocal')
            ->with($alias->getName())
            ->will($this->returnValue($hasAlias));

        if($hasAlias){
            $aliasManager->expects($this->once())
                ->method('get')
                ->with($alias->getName())
                ->will($this->returnValue($alias));
        }

        $aliasManager->expects($this->exactly((int) $willBeRemoved))
            ->method('remove')
            ->with($this->equalTo($alias->getName()));

        return $aliasManager;
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
                        return '<question>Do you want to remove the bootstrap "bootstrap"</question>[y]' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue($result));

        return $questionHelper;
    }
}
