<?php
namespace Samurai\Command;

use Pimple\Container;
use Puppy\Config\Config;
use Samurai\Alias\AliasManager;
use Samurai\Samurai;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class AliasTest
 * @package Samurai\Command
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteSave()
    {
        $samurai = new Samurai(new Application());
        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayNotHasKey('test', $alias);

        $command = $samurai->getApplication()->find('alias');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'bootstrap' => 'vendor/package',
            'name' => 'test',
            'version' => '@stable',
            'description' => 'description',
        ]);

        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayHasKey('test', $alias);
        $this->assertSame('vendor/package', $alias['test']->getBootstrap());
        $this->assertSame('@stable', $alias['test']->getVersion());
        $this->assertSame('description', $alias['test']->getDescription());
    }

    public function testExecuteListDefault()
    {
        $samurai = new Samurai(new Application());
        $command = $samurai->getApplication()->find('alias');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertStringStartsWith(
            '[lib] Basic PHP library (raphhh/php-lib-bootstrap)',
            $commandTester->getDisplay()
        );
    }

    public function testExecuteListGlobal()
    {
        $samurai = new Samurai(new Application());
        $command = $samurai->getApplication()->find('alias');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--global' => true,
        ]);

        $this->assertStringStartsWith(
            '[lib] Basic PHP library (raphhh/php-lib-bootstrap)',
            $commandTester->getDisplay()
        );
    }

    /**
     * @depends testExecuteSave
     */
    public function testExecuteListLocal()
    {
        $samurai = new Samurai(new Application());
        $command = $samurai->getApplication()->find('alias');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--local' => true,
        ]);

        $this->assertStringStartsWith(
            '[test] description (vendor/package @stable)',
            $commandTester->getDisplay()
        );
    }

    /**
     * @depends testExecuteListLocal
     */
    public function testExecuteDeleteNoConfirm()
    {
        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));

        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(false));

        $samurai = new Samurai(new Application(), $this->provideServices($questionHelper));
        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayHasKey('test', $alias);

        $command = $samurai->getApplication()->find('alias');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'test',
        ]);

        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayHasKey('test', $alias);
    }

    /**
     * @depends testExecuteDeleteNoConfirm
     */
    public function testExecuteDeleteConfirm()
    {
        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));

        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will($this->returnValue(true));

        $samurai = new Samurai(new Application(), $this->provideServices($questionHelper));
        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayHasKey('test', $alias);

        $command = $samurai->getApplication()->find('alias');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => 'test',
        ]);

        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayNotHasKey('test', $alias);
    }

    /**
     * @param QuestionHelper $questionHelper
     * @return Container
     * @internal param $result
     */
    private function provideServices(QuestionHelper $questionHelper)
    {
        $services = new Container();

        $services['question'] = function () use ($questionHelper) {
            return $questionHelper;
        };

        $services['alias_manager'] = function () {
            return new AliasManager(new Config(''));
        };
        return $services;
    }
}
