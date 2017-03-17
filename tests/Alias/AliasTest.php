<?php
namespace Samurai\Alias;

use PHPUnit\Framework\TestCase;

/**
 * Class AliasTest
 * @package Samurai\Alias
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasTest extends TestCase
{

    public function testToArray()
    {
        $alias = new Alias();
        $alias->setName('name');
        $alias->setDescription('description');
        $alias->setPackage('bootstrap');
        $alias->setVersion('version');
        $alias->setSource('source');
        $this->assertSame(
            [
              'name' => 'name',
              'description' => 'description',
              'package' => 'bootstrap',
              'version' => 'version',
              'source' => 'source',
            ],
            $alias->toArray()
        );
    }
}
