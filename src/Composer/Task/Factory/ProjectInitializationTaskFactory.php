<?php
namespace Samurai\Composer\Task\Factory;

use Pimple\Container;
use Samurai\Composer\Question\BootstrapQuestion;
use Samurai\Composer\Question\DescriptionQuestion;
use Samurai\Composer\Question\DirectoryPathQuestion;
use Samurai\Composer\Question\HomepageQuestion;
use Samurai\Composer\Question\KeywordsQuestion;
use Samurai\Composer\Question\NameQuestion;
use Samurai\Task\ITask;
use Samurai\Task\Planner;

/**
 * Class ProjectInitialization
 * @package Samurai\Composer\Task\Factory
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
