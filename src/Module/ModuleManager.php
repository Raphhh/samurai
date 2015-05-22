<?php
namespace Samurai\Module;

use Balloon\Balloon;

/**
 * Class ModuleManager
 * @package Samurai\Module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleManager extends Balloon
{

    /**
     * @param string $package
     * @return Modules
     */
    public function getByPackage($package)
    {
        return $this->find(
            function (Module $module) use ($package) {
                return $module->getPackage() === $package;
            }
        );
    }
}
