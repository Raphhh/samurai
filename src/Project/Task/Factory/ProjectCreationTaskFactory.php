<?php
namespace Samurai\Project\Task\Factory;

use Pimple\Container;
use Samurai\Project\Task\ComposerConfigSetting;
use Samurai\Project\Task\FilesCleaning;
use Samurai\Project\Task\ProjectDownload;
use Samurai\Task\ITask;
use Samurai\Task\Planner;

/**
 * Class ProjectCreation
 * @package Samurai\Project\Task\Factory
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
            new ProjectDownload($services),
            new ComposerConfigSetting($services),
            new FilesCleaning($services),
        ]);
    }
}
