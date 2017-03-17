<?php
namespace Samurai\Alias;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Puppy\Config\ArrayConfig;
use Puppy\Config\Config;
use Samurai\Samurai;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class AliasCommandTest
 * @package Samurai\Command
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasCommandTest extends TestCase
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
            'action' => 'save',
            'bootstrap' => 'vendor/package',
            'name' => 'test',
            'version' => '@stable',
            'description' => 'description',
        ]);

        $alias = $samurai->getServices()['alias_manager']->getLocal();
        $this->assertArrayHasKey('test', $alias);
        $this->assertSame('vendor/package', $alias['test']->getPackage());
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
            'action' => 'list',
        ]);

        $this->assertStringStartsWith(
            "name: lib\ndescription: Basic PHP library\npackage: raphhh/php-lib-bootstrap\nversion: \nsource:",
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
            'action' => 'list',
            '--global' => true,
        ]);

        $this->assertStringStartsWith(
            "name: lib\ndescription: Basic PHP library\npackage: raphhh/php-lib-bootstrap\nversion: \nsource:",
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
            'action' => 'list',
            '--local' => true,
        ]);

        $this->assertStringStartsWith(
            "name: test\ndescription: description\npackage: vendor/package\nversion: @stable\nsource:",
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
            'action' => 'rm',
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
            'action' => 'rm',
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

        $services['config'] = function () {
            return new Config('');
        };

        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        $services['alias_manager'] = function () {
            $factory = new AliasManagerFactory();
            return $factory->createFromConfig(new Config(''));
        };
        return $services;
    }
}
