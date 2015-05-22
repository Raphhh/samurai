<?php
namespace Samurai\Module;

/**
 * Class ModulesSorter
 * @package Samurai\module
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModulesSorter 
{
    /**
     * @var Module[]
     */
    private $initial;

    /**
     * @var Module[]
     */
    private $result;

    /**
     * @param Modules $modules
     * @return Modules
     */
    public function sort(Modules $modules)
    {
        $this->result = [];
        $this->initial = $modules->getArrayCopy();
        $this->run();
        return new Modules($this->result);
    }

    /**
     *
     */
    private function run()
    {
        do {
            if (!$this->sortOnce()) {
                throw new \RuntimeException(
                    sprintf(
                        'Modules sort not possible. Maybe recursive dependencies in these modules: %s.',
                        implode(', ', array_keys($this->initial))
                    )
                );
            }

        } while ($this->initial);
    }

    /**
     * @return bool
     */
    private function sortOnce()
    {
        $result = false;
        foreach ($this->initial as $name => $module) {
            if ($this->canBeAdded($module)) {
                $this->result[$name] = $module;
                unset($this->initial[$name]);
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param Module $module
     * @return bool
     */
    private function canBeAdded($module)
    {
        if ($module->getDependencies()->count()) {
            return $this->areAllAlreadyPresent($module->getDependencies());
        }
        return true;
    }

    /**
     * @param Modules $dependencies
     * @return bool
     */
    private function areAllAlreadyPresent(Modules $dependencies)
    {
        $count = 0;
        foreach($dependencies as $iDependency){
            foreach($this->result as $rDependency){
                if($iDependency->getPackage() === $rDependency->getPackage()){
                    $count++;
                    break;
                }
            }
        }
        return $count === count($dependencies);
    }

}
