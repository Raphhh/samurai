<?php
namespace Samurai\Module;

use Samurai\Alias\Alias;

/**
 * Class Module
 * @package Samurai\module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Module extends Alias
{
    /**
     * @var bool
     */
    private $isEnable;

    /**
     * modules which must be executed before the current module.
     *
     * @var Modules
     */
    private $dependencies = [];

    /**
     * commands to run for the current module.
     *
     * @var array
     */
    private $tasks = [];

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setDependencies(new Modules());
    }

    /**
     * Getter of $isEnable
     *
     * @return boolean
     */
    public function isEnable()
    {
        return $this->isEnable;
    }

    /**
     * Getter of $isEnable
     *
     * @return boolean
     */
    public function getIsEnable()
    {
        return $this->isEnable;
    }

    /**
     * Setter of $isEnable
     *
     * @param boolean $isEnable
     */
    public function setIsEnable($isEnable)
    {
        $this->isEnable = (boolean)$isEnable;
    }
    /**
     * Getter of $dependencyNames
     *
     * @return Modules
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Setter of $dependencyNames
     *
     * @param Modules $dependencies
     */
    public function setDependencies($dependencies)
    {
        if(is_array($dependencies)){//todo should not be allowed + unit test with the ModuleManager!
            $dependencies = $this->buildModulesFromData($dependencies);
        }
        $this->dependencies = $dependencies;
    }

    /**
     * Getter of $tasks
     *
     * @return array
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Setter of $tasks
     *
     * @param array $tasks
     */
    public function setTasks(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'isEnable' => $this->isEnable(),
                'tasks' => $this->getTasks(),
                'dependencies' => $this->getDependencies()->toArray()
            ]
        );
    }

    /**
     * @param array $dependenciesData
     * @return Modules
     */
    private function buildModulesFromData(array $dependenciesData)
    {
        $modules = new Modules();
        foreach ($dependenciesData as $key => $dependencyData) {
            $modules[$key] = $this->buildModuleFromData($dependencyData);
        }
        return $modules;
    }

    /**
     * @param array $data
     * @return Module
     */
    private function buildModuleFromData(array $data)
    {
        $module = new Module();
        $module->setName(!empty($data['name']) ? $data['name'] : null);
        $module->setPackage(!empty($data['package']) ? $data['package'] : null);
        $module->setVersion(!empty($data['version']) ? $data['version'] : null);
        $module->setDescription(!empty($data['description']) ? $data['description'] : null);
        $module->setSource(!empty($data['source']) ? $data['source'] : null);
        $module->setIsEnable(true);
        return $module;
    }
}
