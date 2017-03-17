<?php
namespace Samurai\Project;

use PHPUnit\Framework\TestCase;

/**
 * Class AuthorTest
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AuthorTest extends TestCase
{

    public function testConstructorWithEmptyString()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid author "".  Must be in the format: "name <mail@mail.com>"'
        );
        new Author('');
    }

    public function testConstructorWithOnlyName()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid author "James Bond".  Must be in the format: "name <mail@mail.com>"'
        );
        new Author('James Bond');
    }

    public function testConstructorWithOnlyEmail()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid author "james@mi5.co.uk".  Must be in the format: "name <mail@mail.com>"'
        );
        new Author('james@mi5.co.uk');
    }

    public function testConstructorWithFormatRFC822()
    {
        $author = new Author('James Bond <james@mi5.co.uk>');
        $this->assertSame('James Bond', $author->getName());
        $this->assertSame('james@mi5.co.uk', $author->getEmail());
    }

    public function testConstructorWithFormatRFC822ButNotValidMail()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid email "james"'
        );
        new Author('James Bond <james>');
    }

    public function testToArray()
    {
        $author = new Author('James Bond <james@mi5.co.uk>');
        $this->assertSame(
            [
                'name' => 'James Bond',
                'email' => 'james@mi5.co.uk',
            ],
            $author->toArray()
        );
    }

    public function testToString()
    {
        $value = 'James Bond <james@mi5.co.uk>';
        $author = new Author($value);
        $this->assertSame($value, (string)$author);
    }
}
