<?php
namespace Samurai\File;

/**
 * Class ComposerConfigManager
 * @package Samurai\Composer
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class JsonFileManager
{

    /**
     * @param $path
     * @return array|null
     */
    public function get($path)
    {
        if(is_readable($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return null;
    }

    /**
     * @param $path
     * @param array $config
     * @return int
     */
    public function set($path, array $config)
    {
        return file_put_contents(
            $path,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }
}
