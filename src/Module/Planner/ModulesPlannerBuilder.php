<?php
namespace Samurai\Module\Planner;

use Pimple\Container;
use Samurai\Module\Modules;
use Samurai\Task\Planner;

/**
 * Class ModulesPlannerBuilder
 * @package Samurai\Module\Planner
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesPlannerBuilder implements IPlannerBuilder
{
    /**
     * @var Container
     */
    private $services;

    /**
     * @var Modules
     */
    private $modules;

    /**
     * @param Container $services
     * @param Modules $modules
     */
    public function __construct(Container $services, Modules $modules)
    {
        $this->services = $services;
        $this->modules = $modules;
    }

    /**
     * @return Planner
     */
    public function create()
    {
        $planner = new Planner();
        foreach($this->modules as $module){
            $planner[] = new PlannerAdapter(new ModulePlannerBuilder($this->services, $module));
        }
        return $planner;
    }
}
