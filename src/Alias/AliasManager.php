<?php
namespace Samurai\Alias;

use Balloon\Balloon;
use Balloon\Factory\BalloonFactory;
use Puppy\Config\Config;

/**
 * Class AliasManager
 * @package Samurai\alias
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AliasManager
{
    /**
     * @var Balloon
     */
    private $globalManager;

    /**
     * @var Balloon
     */
    private $localManager;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $balloonFactory = new BalloonFactory();
        $this->globalManager = $balloonFactory->create($config['alias.global.path'], 'Samurai\Alias\Alias', 'name');
        $this->localManager = $balloonFactory->create($config['alias.local.path'], 'Samurai\Alias\Alias', 'name');
    }

    /**
     * @param $name
     * @return null|Alias
     */
    public function get($name)
    {
        if($this->has($name)){
            return $this->getAll()[$name];
        }
        return null;
    }

    /**
     * @return Alias[]
     */
    public function getAll()
    {
        return array_merge($this->getGlobal(), $this->getLocal());
    }

    /**
     * @return Alias[]
     */
    public function getGlobal()
    {
        return $this->globalManager->getAll();
    }

    /**
     * @return Alias[]
     */
    public function getLocal()
    {
        return $this->localManager->getAll();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->getAll());
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasGlobal($name)
    {
        return array_key_exists($name, $this->getGlobal());
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasLocal($name)
    {
        return array_key_exists($name, $this->getLocal());
    }

    /**
     * @param array $aliases
     * @return int
     */
    public function addList(array $aliases)
    {
        return $this->localManager->addList($aliases);
    }

    /**
     * @param Alias $alias
     * @return int
     */
    public function add(Alias $alias)
    {
        return $this->localManager->add($alias);
    }

    /**
     * @param string $name
     * @return int
     */
    public function remove($name)
    {
        return $this->localManager->remove($name);
    }

    /**
     * @return int
     */
    public function flush()
    {
        return $this->localManager->flush();
    }
}
