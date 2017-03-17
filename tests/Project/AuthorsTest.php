<?php
namespace Samurai\Project;

use PHPUnit\Framework\TestCase;

/**
 * Class AuthorsTest
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AuthorsTest extends TestCase
{

    public function testToArray()
    {
        $authors = new Authors();
        $authors[] = new Author('James Bond <james@mi5.co.uk>');
        $authors[] = new Author('Indiana Jones <indi@marshall.edu>');
        $this->assertSame(
            [
                [
                    'name' => 'James Bond',
                    'email' => 'james@mi5.co.uk',
                ],
                [
                    'name' => 'Indiana Jones',
                    'email' => 'indi@marshall.edu',
                ],
            ],
            $authors->toArray()
        );
    }
}
