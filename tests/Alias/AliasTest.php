<?php
namespace Samurai\Alias;

/**
 * Class AliasTest
 * @package Samurai\Alias
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasTest extends \PHPUnit_Framework_TestCase
{

    public function testToArray()
    {
        $alias = new Alias();
        $alias->setName('name');
        $alias->setDescription('description');
        $alias->setBootstrap('bootstrap');
        $alias->setVersion('version');
        $this->assertSame(
            [
              'name' => 'name',
              'description' => 'description',
              'bootstrap' => 'bootstrap',
              'version' => 'version',
            ],
            $alias->toArray()
        );
    }
}
