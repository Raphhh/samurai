<?php
namespace Samurai\Alias\Task\Factory;

use Pimple\Container;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Class AliasManagementTaskFactoryTest
 * @package Samurai\Alias\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasManagementTaskFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateWithBootstrap()
    {
        $factory = new AliasManagementTaskFactory();
        $result = $factory->create($this->provideInput(['bootstrap' => 'bootstrap']), new Container());
        $this->assertInstanceOf('Samurai\Alias\Task\Saving', $result);
    }

    public function testCreateWithName()
    {
        $factory = new AliasManagementTaskFactory();
        $result = $factory->create($this->provideInput(['name' => 'name']), new Container());
        $this->assertInstanceOf('Samurai\Alias\Task\Removing', $result);
    }

    public function testCreateDefault()
    {
        $factory = new AliasManagementTaskFactory();
        $result = $factory->create($this->provideInput([]), new Container());
        $this->assertInstanceOf('Samurai\Alias\Task\Listing', $result);
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
                new InputArgument('bootstrap'),
            ])
        );
    }
}
