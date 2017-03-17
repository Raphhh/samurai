<?php
namespace Samurai;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

/**
 * Class SamuraiTest
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class SamuraiTest extends TestCase
{

    public function testRun()
    {
        $application = $this->getMockBuilder('Symfony\Component\Console\Application')->getMock();

        $application->expects($this->once())
            ->method('run')
            ->will($this->returnValue(0));

        $samurai = new Samurai($application);
        $this->assertSame(0, $samurai->run());
    }

    public function testGetServices()
    {
        $samurai = new Samurai(new Application());
        foreach($samurai->getServices()->keys() as $key){
            $this->assertNotNull($samurai->getServices()[$key]);
        }
    }
}
