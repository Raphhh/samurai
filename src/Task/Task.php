<?php
namespace Samurai\Task;

use Pimple\Container;
use Samurai\Service\ServiceWorker;

/**
 * Class Task
 * @package Samurai\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
abstract class Task implements ITask
{
    use ServiceWorker;

    /**
     * @param Container $services
     */
    public function __construct(Container $services)
    {
        $this->setServices($services);
    }
}
