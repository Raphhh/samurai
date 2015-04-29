<?php
namespace Samurai\Alias;

use Balloon\Factory\BalloonFactory;
use Puppy\Config\Config;

/**
 * Class AliasManagerFactory
 * @package Samurai\Alias
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasManagerFactory
{
    /**
     * @param Config $config
     * @return AliasManager
     */
    public function createFromConfig(Config $config)
    {
        $balloonFactory = new BalloonFactory();
        return new AliasManager(
            $balloonFactory->create($config['alias.global.path'], 'Samurai\Alias\Alias', 'name'),
            $balloonFactory->create($config['alias.local.path'], 'Samurai\Alias\Alias', 'name')
        );
    }
}
