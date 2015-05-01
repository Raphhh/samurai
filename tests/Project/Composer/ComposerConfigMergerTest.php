<?php
namespace Samurai\Project\Composer;

use Samurai\Project\Project;

/**
 * Class ComposerConfigMergerTest
 * @package Samurai\Project\Composer
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ComposerConfigMergerTest extends \PHPUnit_Framework_TestCase
{

    public function testMerge()
    {
        $initialConfig = [
            'name' => 'init-name',
            'description' => 'init-description',
            'keywords' => ['init-k1', 'init-k2'],
            'homepage' => 'http://init-homepage.com',
            'version' => '1.0.0',
            'time' => '1999-12-31',
            'authors' => [
                [
                    'name' => 'init-author-name',
                    'email' => 'init-author@email.com',
                ]
            ],
            'autoload' => [
                'psr-4' => [
                    'init-namespace\\' => [
                        'path1',
                        'path2',
                    ],
                ],
            ]
        ];

        $newConfig = [
            'name' => 'new-name',
            'description' => 'new-description',
            'keywords' => ['new-k1', 'new-k2'],
            'homepage' => 'http://new-homepage.com',
            'authors' => [
                [
                    'name' => 'new-author-name',
                    'email' => 'new-author@email.com',
                ]
            ],
            'autoload' => [
                'psr-4' => [
                    'new-namespace\\' => [
                        'path1',
                        'path2',
                    ],
                ],
            ]
        ];

        $composerConfigMerger = new ComposerConfigMerger();
        $result = $composerConfigMerger->merge($initialConfig, $newConfig);
        $this->assertSame(
            [
                'name' => 'new-name',
                'description' => 'new-description',
                'keywords' => ['new-k1', 'new-k2'],
                'homepage' => 'http://new-homepage.com',
                'authors' => [
                    [
                        'name' => 'new-author-name',
                        'email' => 'new-author@email.com',
                    ]
                ],
                'autoload' => [
                    'psr-4' => [
                        'init-namespace\\' => [
                            'path1',
                            'path2',
                        ],
                        'new-namespace\\' => [
                            'path1',
                            'path2',
                        ],
                    ],
                ]
            ],
            $result
        );
    }
}
