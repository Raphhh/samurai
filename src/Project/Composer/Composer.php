<?php
namespace Samurai\Project\Composer;

use Balloon\Balloon;
use Balloon\Factory\BalloonFactory;
use InvalidArgumentException;
use Samurai\Project\Project;
use TRex\Cli\Executor;

/**
 * Class Composer
 * @package Samurai\Project\Composer
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Composer
{
    /**
     * @var Balloon[]
     */
    private $composerConfigManager = [];

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @param Executor $executor
     */
    public function __construct(Executor $executor)
    {
        $this->setExecutor($executor);
    }

    /**
     * @param Project $project
     * @param array $options
     * @return string
     */
    public function createProject(Project $project, array $options = array())
    {
        if(!$project->getBootstrapName()){
            throw new InvalidArgumentException('The bootstrap of the project is not defined');
        }

        return $this->getExecutor()->flush(
            trim(
                sprintf(
                    'composer create-project --prefer-dist %s %s %s',
                    $project->getBootstrapName(),
                    $project->getDirectoryPath(),
                    $project->getBootstrapVersion()
                )
            )
            .$this->mapOptions($options)
        );
    }

    /**
     * @param $cwd
     * @return string
     */
    public function getConfigPath($cwd = '')
    {
        if($cwd){
            $cwd = rtrim($cwd, '/') . '/';
        }
        return  $cwd . 'composer.json';
    }

    /**
     * @param string $cwd
     * @return array
     */
    public function getConfig($cwd = '')
    {
        return $this->getComposerConfigManager($cwd)->getAll();
    }

    /**
     * @param array $config
     * @param string $cwd
     * @return int
     */
    public function setConfig(array $config, $cwd = '')
    {
        $this->getComposerConfigManager($cwd)->removeAll();
        return $this->getComposerConfigManager($cwd)->add($config);
    }

    /**
     * @param string $cwd
     * @return bool
     */
    public function validateConfig($cwd = '')
    {
        return $this->getExecutor()->flush($this->cd($cwd) . 'composer validate');
    }

    /**
     * @param string $cwd
     * @return bool
     */
    public function dumpAutoload($cwd = '')
    {
        return $this->getExecutor()->flush($this->cd($cwd) . 'composer dump-autoload');
    }

    /**
     * @param $cwd
     * @return string
     */
    private function cd($cwd)
    {
        if($cwd) {
            return 'cd ' . $cwd . ' && ';
        }
        return '';
    }

    /**
     * @param array $options
     * @return string
     */
    private function mapOptions(array $options)
    {
        $result = '';
        foreach($options as $option => $value){
            $result .= ' --' . $option . '=' . $value;
        }
        return $result;
    }

    /**
     * Getter of $composerConfigManager
     *
     * @param $cwd
     * @return Balloon
     */
    public function getComposerConfigManager($cwd)
    {
        if(empty($this->composerConfigManager[$cwd])){
            $balloonFactory = new BalloonFactory();
            $this->composerConfigManager[$cwd] = $balloonFactory->create($this->getConfigPath($cwd));
        }
        return $this->composerConfigManager[$cwd];
    }

    /**
     * Getter of $executor
     *
     * @return Executor
     */
    private function getExecutor()
    {
        return $this->executor;
    }

    /**
     * Setter of $executor
     *
     * @param Executor $executor
     */
    private function setExecutor(Executor $executor)
    {
        $this->executor = $executor;
    }
}
