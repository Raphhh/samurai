<?php
namespace Samurai\Module\Planner;

use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Task\Planner;

/**
 * Class ModulePlannerBuilder
 * @package Samurai\Module\Planner
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulePlannerBuilder implements IPlannerBuilder
{
    /**
     * @var Container
     */
    private $services;

    /**
     * @var Module
     */
    private $module;

    /**
     * @param Container $services
     * @param Module $module
     */
    public function __construct(Container $services, Module $module)
    {
        $this->services = $services;
        $this->module = $module;
    }

    /**
     * @return Planner
     */
    public function create()
    {
        $planner = new Planner();
        foreach($this->module->getTasks() as $className){
            $planner[] = new $className($this->services);
        }
        return $planner;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->module->getName();
    }
}
