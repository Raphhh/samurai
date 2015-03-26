<?php
namespace Samurai\Alias;

/**
 * Class Alias
 * @package Samurai\alias
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Alias 
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $bootstrap;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $repository;

    /**
     * Getter of $name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter of $name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * Getter of $description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Setter of $description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
    }

    /**
     * Getter of $bootstrap
     *
     * @return string
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * Setter of $bootstrap
     *
     * @param string $bootstrap
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = (string)$bootstrap;
    }

    /**
     * Getter of $version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Setter of $version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = (string)$version;
    }

    /**
     * Getter of $repository
     *
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Setter of $repository
     *
     * @param string $repository
     */
    public function setRepository($repository)
    {
        $this->repository = (string)$repository;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->getDescription()
        . ' ('
        . trim($this->getBootstrap() . ' ' . $this->getVersion())
        . ')';
    }
}
