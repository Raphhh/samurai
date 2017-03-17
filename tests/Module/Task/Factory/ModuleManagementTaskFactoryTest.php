<?php
namespace Samurai\Module\Task\Factory;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Class ModuleManagementTaskFactoryTest
 * @package Samurai\Module\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleManagementTaskFactoryTest extends TestCase
{

    public function testCreateWithIncorrectAction()
    {
        $input = $this->provideInput(['action' => 'rune']);

        $factory = new ModuleManagementTaskFactory();
        $this->setExpectedException('InvalidArgumentException', 'Action "rune" not supported. Did you mean "run"?');
        $factory->create($input, new Container());
    }

    public function testCreateWithoutAction()
    {
        $input = $this->provideInput(['action' => 'run']);

        $factory = new ModuleManagementTaskFactory();
        $result = $factory->create($input, new Container());
        $this->assertInstanceOf('Samurai\Module\Task\Running', $result);
    }

    public function testCreateWithRunAction()
    {
        $input = $this->provideInput(['action' => 'run', 'name' => 'my-module']);

        $factory = new ModuleManagementTaskFactory();
        $result = $factory->create($input, new Container());
        $this->assertInstanceOf('Samurai\Module\Task\Running', $result);
    }

    public function testCreateWithRemoveAction()
    {
        $input = $this->provideInput(['action' => 'rm', 'name' => 'my-module']);

        $factory = new ModuleManagementTaskFactory();
        $result = $factory->create($input, new Container());
        $this->assertInstanceOf('Samurai\Module\Task\Removing', $result);
    }

    public function testCreateWithUpdateAction()
    {
        $input = $this->provideInput(['action' => 'update', 'name' => 'my-module']);

        $factory = new ModuleManagementTaskFactory();
        $result = $factory->create($input, new Container());
        $this->assertInstanceOf('Samurai\Module\Task\Updating', $result);
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
                new InputArgument('action'),
                new InputArgument('name'),
            ])
        );
    }
}
