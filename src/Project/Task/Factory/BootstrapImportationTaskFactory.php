<?php
namespace Samurai\Project\Task\Factory;

use Pimple\Container;
use Samurai\Project\Task\ComposerConfigSetting;
use Samurai\Project\Task\FilesCleaning;
use Samurai\Project\Task\BootstrapImportation;
use Samurai\Project\Task\ModulesRunning;
use Samurai\Task\ITask;
use Samurai\Task\Planner;

/**
 * Class ProjectCreation
 * @package Samurai\Project\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class BootstrapImportationTaskFactory
{
    /**
     * @param Container $services
     * @return ITask
     */
    public function create(Container $services)
    {
        return new Planner([
            ProjectInitializationTaskFactory::create($services),
            new BootstrapImportation($services),
            new ComposerConfigSetting($services),
            new FilesCleaning($services),
            new ModulesRunning($services),
        ]);
    }
}
