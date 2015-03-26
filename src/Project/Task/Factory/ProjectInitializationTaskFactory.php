<?php
namespace Samurai\Project\Task\Factory;

use Pimple\Container;
use Samurai\Project\Question\BootstrapQuestion;
use Samurai\Project\Question\DescriptionQuestion;
use Samurai\Project\Question\DirectoryPathQuestion;
use Samurai\Project\Question\HomepageQuestion;
use Samurai\Project\Question\KeywordsQuestion;
use Samurai\Project\Question\NameQuestion;
use Samurai\Task\ITask;
use Samurai\Task\Planner;

/**
 * Class ProjectInitialization
 * @package Samurai\Project\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ProjectInitializationTaskFactory
{
    /**
     * @param Container $services
     * @return ITask
     */
    public static function create(Container $services)
    {
        return new Planner([
            new BootstrapQuestion($services),
            new NameQuestion($services),
            new DirectoryPathQuestion($services),
            new DescriptionQuestion($services),
            new HomepageQuestion($services),
            new KeywordsQuestion($services),
        ]);
    }
}
