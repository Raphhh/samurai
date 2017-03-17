<?php
namespace Samurai\Project;

use PHPUnit\Framework\TestCase;

/**
 * Class PackagesTest
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class PackagesTest extends TestCase
{

    public function testToArray()
    {
        $packages = new Packages();
        $packages[] = $this->buildPackage('namespace1', ['path1.1', 'path1.2']);
        $packages[] = $this->buildPackage('namespace2', ['path2.1', 'path2.2']);
        $this->assertSame(
            [
                'psr-4' => [
                    'namespace1\\' => [
                        'path1.1',
                        'path1.2',
                    ],
                    'namespace2\\' => [
                        'path2.1',
                        'path2.2',
                    ],
                ],
            ],
            $packages->toArray()
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
