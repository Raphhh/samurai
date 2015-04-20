<?php
namespace Samurai\File;

/**
 * Class ComposerConfigManager
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class JsonFileManager //todo rename in bridge
{

    /**
     * @param $path
     * @return array|null
     */
    public function get($path) //todo rename read + todo setthe path in the construct
    {
        if(is_readable($path)) {
            return json_decode(file_get_contents($path), true); //todo set a proxy
        }
        return null;
    }

    /**
     * @param $path
     * @param array $config
     * @return int
     */
    public function set($path, array $config) //todo rename write
    {
        return file_put_contents(
            $path,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
