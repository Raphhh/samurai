<?php
namespace Samurai\Alias\Task;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Alias\Alias;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ListingTest
 * @package Samurai\Alias\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ListingTest extends TestCase
{

    public function testExecuteWithGlobal()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput(['--global' => '1']);
        $output = new BufferedOutput();

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'getGlobal'
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\npackage: bootstrap\nversion: version\nsource: source\n", $output->fetch());
    }

    public function testExecuteWithLocal()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput(['--local' => '1']);
        $output = new BufferedOutput();

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'getLocal'
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\npackage: bootstrap\nversion: version\nsource: source\n", $output->fetch());
    }

    public function testExecuteWithAll()
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

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'getAll'
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\npackage: bootstrap\nversion: version\nsource: source\n", $output->fetch());
    }

    public function testExecuteWithOne()
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

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'get'
            )
        );
        $this->assertSame(ITask::NO_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\npackage: bootstrap\nversion: version\nsource: source\n", $output->fetch());
    }

    public function testExecuteWithOneNotExisting()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
            'source' => 'source',
        ];
        $input = $this->provideInput(['name' => 'my-alias']);
        $output = new BufferedOutput();

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'get',
                false
            )
        );
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $saving->execute($input, $output));
        $this->assertSame("Alias \"my-alias\" not found! Did you mean \"name\"?\n", $output->fetch());
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
                new InputOption('global'),
                new InputOption('local'),
                new InputArgument('name'),
            ])
        );
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
        $alias->setSource($args['source']);
        return $alias;
    }

    /**
     * @param Alias $alias
     * @param string $useMethod
     * @param bool $hasAlias
     * @return Container
     */
    private function provideServices(Alias $alias, $useMethod, $hasAlias = true)
    {
        $services = new Container();

        $aliasManager = $this->provideAliasManager($alias, $useMethod, $hasAlias);
        $services['alias_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        return $services;
    }

    /**
     * @param Alias $alias
     * @param string $useMethod
     * @param bool $hasAlias
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideAliasManager(Alias $alias, $useMethod, $hasAlias)
    {
        $aliasManager = $this->getMockBuilder('Samurai\Alias\AliasManager')
            ->disableOriginalConstructor()
            ->getMock();

        $aliasManager->expects($this->exactly((int) ($useMethod === 'getGlobal')))
            ->method('getGlobal')
            ->will($this->returnValue([$alias->getName() => $alias]));

        $aliasManager->expects($this->exactly((int) ($useMethod === 'getLocal')))
            ->method('getLocal')
            ->will($this->returnValue([$alias->getName() => $alias]));

        $aliasManager->expects($this->exactly((int) ($useMethod === 'getAll' || !$hasAlias)))
            ->method('getAll')
            ->will($this->returnValue([$alias->getName() => $alias]));

        $aliasManager->expects($this->exactly((int) ($useMethod === 'get' && $hasAlias)))
            ->method('get')
            ->will($this->returnValue($alias));

        $aliasManager->expects($this->exactly((int) ($useMethod === 'get')))
            ->method('has')
            ->will($this->returnValue($hasAlias));

        return $aliasManager;
    }
}
