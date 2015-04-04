<?php
namespace Samurai\Project;

/**
 * Class Package
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Package 
{
    /**
     * @var string
     */
    private $psr = 'psr-4';

    /**
     * @var string
     */
    private $namespace = '';

    /**
     * @var array
     */
    private $pathList = [];

    /**
     * Getter of $psr
     *
     * @return string
     */
    public function getPsr()
    {
        return $this->psr;
    }

    /**
     * Setter of $psr
     *
     * @param string $psr
     */
    public function setPsr($psr)
    {
        $this->psr = $psr;
    }

    /**
     * Getter of $namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Setter of $namespace
     *
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = trim($namespace, '\\') . '\\';
    }

    /**
     * Getter of $pathList
     *
     * @return array
     */
    public function getPathList()
    {
        return $this->pathList;
    }

    /**
     * Setter of $pathList
     *
     * @param array $pathList
     */
    public function setPathList(array $pathList)
    {
        $this->pathList = $pathList;
    }

    /**
     * @param $path
     */
    public function addPath($path)
    {
        $this->pathList[] = $path;
    }
}
