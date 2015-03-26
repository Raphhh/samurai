<?php
namespace Samurai\Project;

/**
 * Class ProjectTest
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ProjectTest extends \PHPUnit_Framework_TestCase
{

    public function testTpConfig()
    {
        $project = new Project();
        $project->setName('name');
        $project->setDescription('description');
        $project->setKeywords(['k1', 'k2']);
        $project->setHomepage('http://homepage.com');
        $project->setAuthorName('author-name');
        $project->setAuthorEmail('author@email.com');

        $this->assertSame(
            [
                'name' => 'name',
                'description' => 'description',
                'keywords' => ['k1', 'k2'],
                'homepage' => 'http://homepage.com',
                'authors' => [
                    [
                        'name' => 'author-name',
                        'email' => 'author@email.com',
                    ]
                ],
            ],
            $project->toConfig()
        );
    }
}
