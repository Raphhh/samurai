<?php
namespace Samurai\Project\Composer;

/**
 * Class ComposerConfigMerger
 * @package Samurai\Project\Composer
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ComposerConfigMerger 
{
    /**
     * @var string[]
     */
    private static $notValidKeys = [
        'version',
        'time',
    ];

    /**
     * @var string[]
     */
    private static $sharedKeys = [
        'autoload',
    ];

    /**
     * @param array $initialConfig
     * @param array $updatedConfig
     * @return array
     */
    public function merge(array $initialConfig, array $updatedConfig)
    {
        return $this->cleanConfig($this->mergeConfig($initialConfig, $updatedConfig));
    }

    /**
     * @param array $config
     * @return array
     */
    private function cleanConfig(array $config)
    {
        return $this->filterArray($this->unsetNotValidKeys($config));
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function filter($value){
        if(is_array($value)){
            return $this->filterArray($value);
        }
        return $value;
    }

    /**
     * @param array $values
     * @return array
     */
    private function filterArray(array $values)
    {
        $result = [];
        foreach ($values as $key => $value) {
            if ($this->filter($value)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @param array $config
     * @return array
     */
    private function unsetNotValidKeys(array $config)
    {
        foreach(self::$notValidKeys as $key){
            if (array_key_exists($key, $config)) {
                unset($config[$key]);
            }
        }
        return $config;
    }

    /**
     * @param array $initialConfig
     * @param array $updatedConfig
     * @return array
     */
    private function mergeConfig(array $initialConfig, array $updatedConfig)
    {
        $updatedConfig = array_merge($initialConfig, $updatedConfig);

        foreach(self::$sharedKeys as $key){
            if (isset($initialConfig[$key])) {
                $updatedConfig[$key] = array_merge_recursive(
                    $initialConfig[$key],
                    $updatedConfig[$key]
                );
            }
        }

        return $updatedConfig;
    }
}
