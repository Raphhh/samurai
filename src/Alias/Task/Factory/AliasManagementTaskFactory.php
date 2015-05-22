<?php
namespace Samurai\Alias\Task\Factory;

use Pimple\Container;
use Samurai\Alias\Task\Listing;
use Samurai\Alias\Task\Removing;
use Samurai\Alias\Task\Saving;
use Samurai\Task\ITask;
use SimilarText\Finder;
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
    public function create(InputInterface $input, Container $services)
    {
        if($input->getArgument('action') === 'list'){
            return new Listing($services);
        }
        if($input->getArgument('action') === 'save'){
            return new Saving($services);
        }
        if($input->getArgument('action') === 'rm' || $input->getArgument('action') === 'remove'){
            return new Removing($services);
        }

        $textFinder = new Finder($input->getArgument('action'), ['save', 'list', 'rm', 'remove']);
        throw new \InvalidArgumentException(sprintf(
            'Action "%s" not supported. Did you mean "%s"?',
            $input->getArgument('action'),
            $textFinder->first()
        ));
    }
}
