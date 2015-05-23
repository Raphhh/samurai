<?php
namespace Samurai\Module;

/**
 * Class ModuleTest
 * @package Samurai\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    public function testRetrieveDependentsWithEmptyModules()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setPackage('p/a');
        $modules['a'] = $moduleA;

        $result = $moduleA->retrieveDependents($modules);
        $this->assertSame([], array_keys($result->getArrayCopy()));
    }


    public function testRetrieveDependents()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setPackage('p/a');
        $modules['a'] = $moduleA;

        $moduleB = new Module();
        $moduleB->setPackage('p/b');
        $modules['b'] = $moduleB;

        $moduleC = new Module();
        $moduleC->setPackage('p/c');
        $moduleC->setDependencies(new Modules([$moduleA]));
        $modules['c'] = $moduleC;

        $result = $moduleA->retrieveDependents($modules);
        $this->assertSame(['c'], array_keys($result->getArrayCopy()));
    }
}
