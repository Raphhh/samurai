<?php
namespace Samurai\Alias\Task;

use Pimple\Container;
use Samurai\Alias\Alias;
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
class ListingTest extends \PHPUnit_Framework_TestCase
{

    public function testExecuteWithGlobal()
    {
        $args = [
            '--global' => '1',
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'getGlobal'
            )
        );
        $this->assertTrue($saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\nbootstrap: bootstrap\nversion: version\nsource:\n", $output->fetch());
    }

    public function testExecuteWithLocal()
    {
        $args = [
            '--local' => '1',
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'getLocal'
            )
        );
        $this->assertTrue($saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\nbootstrap: bootstrap\nversion: version\nsource:\n", $output->fetch());
    }

    public function testExecuteWithAll()
    {
        $args = [
            'name' => 'name',
            'description' => 'description',
            'bootstrap' => 'bootstrap',
            'version' => 'version',
        ];
        $input = $this->provideInput($args);
        $output = new BufferedOutput();

        $saving = new Listing(
            $this->provideServices(
                $this->provideAlias($args),
                'getAll'
            )
        );
        $this->assertTrue($saving->execute($input, $output));
        $this->assertSame("name: name\ndescription: description\nbootstrap: bootstrap\nversion: version\nsource:\n", $output->fetch());
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
                new InputArgument('description'),
                new InputArgument('bootstrap'),
                new InputArgument('version'),
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
        $alias->setBootstrap($args['bootstrap']);
        $alias->setVersion($args['version']);
        return $alias;
    }

    /**
     * @param Alias $alias
     * @param string $useMethod
     * @return Container
     */
    private function provideServices(Alias $alias, $useMethod)
    {
        $services = new Container();

        $aliasManager = $this->provideAliasManager($alias, $useMethod);
        $services['alias_manager'] = function () use($aliasManager){
            return $aliasManager;
        };

        return $services;
    }

    /**
     * @param Alias $alias
     * @param string $useMethod
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideAliasManager(Alias $alias, $useMethod)
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

        $aliasManager->expects($this->exactly((int) ($useMethod === 'getAll')))
            ->method('getAll')
            ->will($this->returnValue([$alias->getName() => $alias]));

        return $aliasManager;
    }
}
