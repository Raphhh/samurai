<?php
namespace Samurai\Alias\Task\Factory;

use Pimple\Container;
use Samurai\Alias\Task\Listing;
use Samurai\Alias\Task\Removing;
use Samurai\Alias\Task\Saving;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class TaskFactory
 * @package Samurai\Alias\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasManagementTaskFactory
{
    /**
     * @param InputInterface $input
     * @param Container $services
     * @return ITask
     */
    public static function create(InputInterface $input, Container $services) //todo avoid static
    {
        if($input->getArgument('bootstrap')){
            return new Saving($services);
        }
        if($input->getArgument('name')){
            return new Removing($services);
        }
        return new Listing($services);
    }
}
