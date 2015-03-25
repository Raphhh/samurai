<?php
namespace Samurai\Command;

use Pimple\Container;
use Samurai\Service\ServiceWorker;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Class Command
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Command extends SymfonyCommand
{
    use ServiceWorker;

    /**
     * @param Container $services
     */
    public function __construct(Container $services)
    {
        $this->setServices($services);
        parent::__construct();
    }
}
