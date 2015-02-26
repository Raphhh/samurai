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

        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $this->assertEquals(
            $this->provideGlobalAlias(),
            $aliasManager->getGlobal()
        );
    }

    public function testGetLocal()
    {
        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $this->assertEquals([], $aliasManager->getLocal());
    }

    public function testGetAll()
    {
        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $this->assertEquals(
            $this->provideGlobalAlias(),
            $aliasManager->getAll()
        );
    }

    public function testAddAndRemove()
    {
        $alias = new Alias();
        $alias->setName('name');
        $alias->setDescription('desc');
        $alias->setBootstrap('boots');
        $alias->setVersion('vers');

        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));

        //add
        $aliasManager->add($alias);

        $this->assertEquals(
            [
                'name' => $alias,
            ],
            $aliasManager->getLocal()
        );

        $globalAlias = $this->provideGlobalAlias();

        $this->assertEquals(
            $globalAlias,
            $aliasManager->getGlobal()
        );

        $globalAlias['name'] = $alias;
        $this->assertEquals(
            $globalAlias,
            $aliasManager->getAll()
        );

        //remove
        $aliasManager->remove($alias->getName());
        $this->assertEquals([], $aliasManager->getLocal());
        $this->assertEquals(
            $this->provideGlobalAlias(),
            $aliasManager->getGlobal()
        );
    }

    public function testAddAndRemoveWithOverride()
    {
        $alias = new Alias();
        $alias->setName('lib');
        $alias->setDescription('desc');
        $alias->setBootstrap('boots');
        $alias->setVersion('vers');

        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));

        //add
        $aliasManager->add($alias);

        $this->assertEquals(
            [
                'lib' => $alias,
            ],
            $aliasManager->getLocal()
        );

        $globalAlias = $this->provideGlobalAlias();
        $this->assertEquals(
            $globalAlias,
            $aliasManager->getGlobal()
        );

        $globalAlias['lib'] = $alias;
        $this->assertEquals(
            $globalAlias,
            $aliasManager->getAll()
        );

        //remove
        $aliasManager->remove($alias->getName());
        $this->assertEquals([], $aliasManager->getLocal());
        $this->assertEquals(
            $this->provideGlobalAlias(),
            $aliasManager->getGlobal()
        );

        $this->assertEquals(
            $this->provideGlobalAlias(),
            $aliasManager->getAll()
        );
    }

    public function testHasTrue()
    {
        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $this->assertTrue($aliasManager->has('lib'));
    }

    public function testHasFalse()
    {
        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $this->assertFalse($aliasManager->has('none'));
    }

    public function testGetTrue()
    {
        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $result = $aliasManager->get('lib');
        $this->assertSame('lib', $result->getName());
    }

    public function testGetFalse()
    {
        $aliasManager = new AliasManager(new Config('', __DIR__ . '/../../config'));
        $this->assertNull($aliasManager->get('none'));
    }

    /**
     * @return array
     */
    private function provideGlobalAlias()
    {
        return [
            'lib' => $this->provideAlias('lib', 'Basic PHP library','raphhh/php-lib-bootstrap', ''),
            'puppy' => $this->provideAlias('puppy', 'Puppy application','raphhh/puppy', ''),
            'symfony' => $this->provideAlias('symfony', 'Symfony application','symfony/framework-standard-edition', ''),
        ];
    }

    /**
     * @param $name
     * @param $desc
     * @param $bootstrap
     * @param $version
     * @return Alias
     */
    private function provideAlias($name, $desc, $bootstrap, $version)
    {
        $alias = new Alias();
        $alias->setName($name);
        $alias->setDescription($desc);
        $alias->setBootstrap($bootstrap);
        $alias->setVersion($version);
        return $alias;
    }
}
