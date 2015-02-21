<?php
namespace Samurai\Composer\Task\Factory;

use Pimple\Container;
use Samurai\Task\ITask;
use Samurai\Task\Planner;

/**
 * Class ProjectCreation
 * @package Samurai\Composer\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ProjectCreationTaskFactory
{
    /**
     * @param Container $services
     * @return ITask
     */
    public static function create(Container $services)
    {
        return new Planner([
            ProjectInitializationTaskFactory::create($services),
        ]);
    }
}
