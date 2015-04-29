<?php
namespace Samurai\Alias;

use Puppy\Config\Config;

/**
 * Class AliasManagerTest
 * @package Samurai\Alias
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetGlobal()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $this->assertEquals(
            $this->provideAlias(
                'lib',
                'Basic PHP library',
                'raphhh/php-lib-bootstrap',
                '',
                'https://github.com/Raphhh/php-lib-bootstrap'
            ),
            $aliasManager->getGlobal()['lib']
        );
    }

    public function testGetLocal()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $this->assertEquals([], $aliasManager->getLocal());
    }

    public function testGetAll()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $this->assertEquals(
            $this->provideAlias(
                'lib',
                'Basic PHP library',
                'raphhh/php-lib-bootstrap',
                '',
                'https://github.com/Raphhh/php-lib-bootstrap'
            ),
            $aliasManager->getAll()['lib']
        );
    }

    public function testAddAndRemove()
    {
        $alias = new Alias();
        $alias->setName('name');
        $alias->setDescription('desc');
        $alias->setBootstrap('boots');
        $alias->setVersion('vers');

        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));

        //pre-assertion
        $this->assertArrayNotHasKey('name', $aliasManager->getGlobal());
        $this->assertArrayNotHasKey('name', $aliasManager->getLocal());
        $this->assertArrayNotHasKey('name', $aliasManager->getAll());

        //add
        $aliasManager->add($alias);

        $this->assertArrayNotHasKey('name', $aliasManager->getGlobal());
        $this->assertArrayHasKey('name', $aliasManager->getLocal());
        $this->assertArrayHasKey('name', $aliasManager->getAll());

        //remove
        $aliasManager->remove($alias->getName());

        $this->assertArrayNotHasKey('name', $aliasManager->getGlobal());
        $this->assertArrayNotHasKey('name', $aliasManager->getLocal());
        $this->assertArrayNotHasKey('name', $aliasManager->getAll());
    }

    public function testAddAndRemoveWithOverride()
    {
        $alias = new Alias();
        $alias->setName('lib');
        $alias->setDescription('desc');
        $alias->setBootstrap('boots');
        $alias->setVersion('vers');

        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));

        //pre-assertion
        $this->assertArrayHasKey('lib', $aliasManager->getGlobal());
        $this->assertArrayNotHasKey('lib', $aliasManager->getLocal());
        $this->assertArrayHasKey('lib', $aliasManager->getAll());

        //add
        $aliasManager->add($alias);

        $this->assertArrayHasKey('lib', $aliasManager->getGlobal());
        $this->assertArrayHasKey('lib', $aliasManager->getLocal());
        $this->assertArrayHasKey('lib', $aliasManager->getAll());

        //remove
        $aliasManager->remove($alias->getName());

        $this->assertArrayHasKey('lib', $aliasManager->getGlobal());
        $this->assertArrayNotHasKey('lib', $aliasManager->getLocal());
        $this->assertArrayHasKey('lib', $aliasManager->getAll());
    }

    public function testHasTrue()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $this->assertTrue($aliasManager->has('lib'));
    }

    public function testHasFalse()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $this->assertFalse($aliasManager->has('none'));
    }

    public function testGetTrue()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $result = $aliasManager->get('lib');
        $this->assertSame('lib', $result->getName());
    }

    public function testGetFalse()
    {
        $factory = new AliasManagerFactory();
        $aliasManager = $factory->createFromConfig(new Config(''));
        $this->assertNull($aliasManager->get('none'));
    }

    /**
     * @param $name
     * @param $desc
     * @param $bootstrap
     * @param $version
     * @param $source
     * @return Alias
     */
    private function provideAlias($name, $desc, $bootstrap, $version, $source)
    {
        $alias = new Alias();
        $alias->setName($name);
        $alias->setDescription($desc);
        $alias->setBootstrap($bootstrap);
        $alias->setVersion($version);
        $alias->setSource($source);
        return $alias;
    }
}
