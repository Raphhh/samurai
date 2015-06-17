<?php
namespace Samurai\Module\Planner;

use Pimple\Container;
use Samurai\Module\Modules;
use Samurai\Task\Planner;
use Symfony\Component\Console\Helper\QuestionHelper;

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
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @param Container $services
     * @param Modules $modules
     * @param QuestionHelper $questionHelper
     */
    public function __construct(Container $services, Modules $modules, QuestionHelper $questionHelper)
    {
        $this->services = $services;
        $this->modules = $modules;
        $this->questionHelper = $questionHelper;
    }

    /**
     * @return Planner
     */
    public function create()
    {
        $planner = new Planner();
        foreach($this->modules as $module){
            $planner[] = new PlannerAdapter(
                new ModulePlannerBuilder($this->services, $module),
                $this->questionHelper
            );
        }
        return $planner;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->modules);
    }
}
