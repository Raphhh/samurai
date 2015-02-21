<?php
namespace Samurai;

/**
 * Class SamuraiTest
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class SamuraiTest extends \PHPUnit_Framework_TestCase
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
}
