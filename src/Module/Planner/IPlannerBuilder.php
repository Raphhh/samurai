<?php
namespace Samurai\Module\Planner;

use Samurai\Task\Planner;

/**
 * Interface IPlannerBuilder
 * @package Samurai\Module\Planner
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
interface IPlannerBuilder
{
    /**
     * @return Planner
     */
    public function create();
}
