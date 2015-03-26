<?php
namespace Samurai\Alias\Task;

use Pimple\Container;
use Samurai\Alias\Alias;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class SavingTest
 * @package Samurai\Alias\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class SavingTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildAlias()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Saving(
            $this->provideServices(
                $this->provideAlias($args),
                false,
                true,
                $input,
                $output
            )
        );
        $this->assertTrue($saving->execute($input, $output));
    }

    public function testBuildAliasWithoutOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Saving(
            $this->provideServices(
                $this->provideAlias($args),
                true,
                false,
                $input,
                $output
            )
        );
        $this->assertTrue($saving->execute($input, $output));
    }

    public function testBuildAliasWithOverride()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Saving(
            $this->provideServices(
                $this->provideAlias($args),
                true,
                true,
                $input,
                $output
            )
        );
        $this->assertTrue($saving->execute($input, $output));
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
                new InputArgument('source'),
            ])
        );
    }

    /**
     * @param Alias $alias
     * @param bool $hasAlias
     * @param bool $willBeSaved
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Container
     */
    private function provideServices(Alias $alias, $hasAlias, $willBeSaved, InputInterface $input, OutputInterface $output)
    {
        $services = new Container();

        $aliasManager = $this->provideAliasManager($alias, $hasAlias, $willBeSaved);
        $services['alias_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        if($hasAlias) {
            $questionHelper = $this->provideQuestionHelper($input, $output, $willBeSaved);
            $services['question'] = function () use ($questionHelper) {
                return $questionHelper;
            };
        }

        return $services;
    }

    private function provideAlias(array $args)
    {
        $alias = new Alias();
        $alias->setName($args['name']);
        $alias->setDescription($args['description']);
        $alias->setBootstrap($args['bootstrap']);
        $alias->setVersion($args['version']);
        $alias->setSource($args['source']);
        return $alias;
    }

    /**
     * @param Alias $newAlias
     * @param $hasAlias
     * @param bool $willBeSaved
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideAliasManager(Alias $newAlias, $hasAlias, $willBeSaved)
    {
        $oldAlias = clone $newAlias;
        $oldAlias->setDescription($newAlias->getDescription() . '_old');
        $oldAlias->setBootstrap($newAlias->getBootstrap() . '_old');
        $oldAlias->setVersion($newAlias->getVersion() . '_old');

        $aliasManager = $this->getMockBuilder('Samurai\Alias\AliasManager')
            ->disableOriginalConstructor()
            ->getMock();

        $aliasManager->expects($this->once())
            ->method('has')
            ->with($newAlias->getName())
            ->will($this->returnValue($hasAlias));

        $aliasManager->expects($this->exactly((int) $willBeSaved))
            ->method('add')
            ->with($this->equalTo($newAlias));

        $aliasManager->expects($this->any())
            ->method('get')
            ->with($newAlias->getName())
            ->will($this->returnValue($oldAlias));

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
                        return '<question>Do you want to override the bootstrap "bootstrap_old version_old" with "bootstrap version" </question>[y]' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue($result));

        return $questionHelper;
    }
}
