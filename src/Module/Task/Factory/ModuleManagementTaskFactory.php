<?php
namespace Samurai\Module\Task\Factory;

use Pimple\Container;
use Samurai\Module\Task\Listing;
use Samurai\Module\Task\Running;
use Samurai\Module\Task\Saving;
use Samurai\Task\ITask;
use SimilarText\Finder;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class TaskFactory
 * @package Samurai\Alias\Task\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleManagementTaskFactory
{
    /**
     * @param InputInterface $input
     * @param Container $services
     * @return ITask
     */
    public static function create(InputInterface $input, Container $services)
    {
        if($input->getArgument('action') === 'list'){
            return new Listing($services);
        }
        if($input->getArgument('action') === 'save'){
            return new Saving($services);
        }
        if($input->getArgument('action') === 'remove'){
            throw new \RuntimeException('sorry, not yet set');
        }
        if($input->getArgument('action') === 'enable'){
            throw new \RuntimeException('sorry, not yet set');
        }
        if($input->getArgument('action') === 'disable'){
            throw new \RuntimeException('sorry, not yet set');
        }
        if($input->getArgument('action') === 'run' || !$input->getArgument('action')){
            return new Running($services);
        }

        $textFinder = new Finder($input->getArgument('action'), ['save', 'remove', 'list', 'enable', 'disable', 'run']);
        throw new \InvalidArgumentException(sprintf(
            'Action "%s" not supported. Did you mean "%s"?',
            $input->getArgument('action'),
            $textFinder->first()
        ));
    }
}
