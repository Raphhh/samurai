<?php
namespace Samurai\Module;

/**
 * Class ModulesSorterTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesSorterTest extends \PHPUnit_Framework_TestCase
{

    public function testSort()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setPackage('a');

        $moduleB = new Module();
        $moduleB->setPackage('b');

        $moduleC = new Module();
        $moduleC->setPackage('c');

        $moduleB->setDependencies(new Modules([$moduleC, $moduleA]));
        $moduleC->setDependencies(new Modules([$moduleA]));

        $modules['a'] = $moduleA;
        $modules['b'] = $moduleB;
        $modules['c'] = $moduleC;

        $sorter = new ModulesSorter();
        $result = $sorter->sort($modules);
        $this->assertSame(
            ['a', 'c', 'b'],
            array_keys($result->getArrayCopy())
        );
    }

    public function testSortNoDependencies()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setPackage('a');

        $moduleB = new Module();
        $moduleB->setPackage('b');

        $moduleC = new Module();
        $moduleC->setPackage('c');

        $modules['a'] = $moduleA;
        $modules['b'] = $moduleB;
        $modules['c'] = $moduleC;

        $sorter = new ModulesSorter();
        $result = $sorter->sort($modules);
        $this->assertSame(
            ['a', 'b', 'c'],
            array_keys($result->getArrayCopy())
        );
    }

    public function testSortWithEmptyModules()
    {
        $modules = new Modules();

        $sorter = new ModulesSorter();
        $result = $sorter->sort($modules);
        $this->assertSame([], $result->getArrayCopy());
    }

    public function testSortCircularDependencies()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setPackage('a');

        $moduleB = new Module();
        $moduleB->setPackage('b');

        $moduleC = new Module();
        $moduleC->setPackage('c');

        $moduleB->setDependencies(new Modules([$moduleC]));
        $moduleC->setDependencies(new Modules([$moduleB]));

        $modules['a'] = $moduleA;
        $modules['b'] = $moduleB;
        $modules['c'] = $moduleC;

        $sorter = new ModulesSorter();

        $this->setExpectedException(
            'RuntimeException',
            'Modules sort not possible. Maybe circular dependencies between these modules: b, c.'
        );
        $sorter->sort($modules);
    }
}
