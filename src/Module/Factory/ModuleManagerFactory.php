<?php
namespace Samurai\Module\Factory;

use Balloon\Factory\BalloonFactory;

/**
 * Class ModuleManagerFactory
 * @package Samurai\Module\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleManagerFactory extends BalloonFactory
{
    /**
     * @param string $filePath
     * @param string $className
     * @param string $primaryKey
     * @return \Samurai\Module\ModuleManager
     */
    public function create($filePath, $className = 'Samurai\Module\Module', $primaryKey = 'name')
    {
        return parent::create($filePath, $className, $primaryKey);
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return 'Samurai\Module\ModuleManager';
    }
}
