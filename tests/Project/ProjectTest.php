<?php
namespace Samurai\Project;

use PHPUnit\Framework\TestCase;

/**
 * Class ProjectTest
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ProjectTest extends TestCase
{

    public function testTpConfig()
    {
        $project = new Project();
        $project->setName('name');
        $project->setDescription('description');
        $project->setKeywords(['k1', 'k2']);
        $project->setHomepage('http://homepage.com');
        $project->addAuthor(new Author('author-name <author@email.com>'));
        $project->addPackage($this->buildPackage('namespace1', ['path1.1', 'path1.2']));

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
                'autoload' => [
                    'psr-4' => [
                        'namespace1\\' => [
                            'path1.1',
                            'path1.2',
                        ],
                    ],
                ]
            ],
            $project->toConfig()
        );
    }

    /**
     * @param string $namespace
     * @param array $pathList
     * @return Package
     */
    private function buildPackage($namespace, array $pathList)
    {
        $package = new Package();
        $package->setNamespace($namespace);
        $package->setPathList($pathList);
        return $package;
    }
}
