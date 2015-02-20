<?php
namespace Samurai\Service;

use Pimple\Container;

/**
 * Class ServiceWorker
 * @package Samurai\Service
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
trait ServiceWorker
{
    /**
     * @var Container
     */
    private $services;

    /**
     * Getter of $services
     *
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Getter of $services
     *
     * @param $service
     * @return mixed
     */
    public function getService($service)
    {
        return $this->services[$service];
    }

    /**
     * Setter of $services
     *
     * @param Container $services
     */
    protected function setServices(Container $services)
    {
        $this->services = $services;
    }
}
