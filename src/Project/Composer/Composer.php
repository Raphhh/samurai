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
     * @var BalloonFactory
     */
    private $balloonFactory;

    /**
     * @param Executor $executor
     * @param BalloonFactory $balloonFactory
     */
    public function __construct(Executor $executor, BalloonFactory $balloonFactory)
    {
        $this->setExecutor($executor);
        $this->setBalloonFactory($balloonFactory);
    }

    /**
     * @param Project $project
     * @param array $options
     * @return int
     */
    public function createProject(Project $project, array $options = array())
    {
        if(!$project->getBootstrap()){
            throw new InvalidArgumentException('The bootstrap of the project is not defined');
        }

        return $this->execute(
            trim(
                sprintf(
                    'composer create-project --prefer-dist %s %s %s',
                    $project->getBootstrap()->getPackage(),
                    $project->getDirectoryPath(),
                    $project->getBootstrap()->getVersion()
                )
            )
            .$this->mapOptions($options)
        );
    }

    /**
     * @param string $name
     * @param string $version
     * @param bool $isGlobal
     * @param array $options
     * @return int
     */
    public function requirePackage($name, $version = '', $isGlobal = false, array $options = array())
    {
        $global = $isGlobal ? 'global ' : '';
        return $this->execute(
            trim(sprintf('composer %srequire %s %s', $global, $name, $version))
            . $this->mapOptions($options)
        );
    }

    /**
     * @param $name
     * @param bool $isGlobal
     * @param array $options
     * @return int
     */
    public function updatePackage($name, $isGlobal = false, array $options = array())
    {
        $global = $isGlobal ? 'global ' : '';
        return $this->execute(
            trim(sprintf('composer %supdate %s', $global, $name))
            . $this->mapOptions($options)
        );
    }

    /**
     * @param string $name
     * @param bool $isGlobal
     * @param array $options
     * @return int
     */
    public function removePackage($name, $isGlobal = false, array $options = array())
    {
        $global = $isGlobal ? 'global ' : '';
        return $this->execute(
            trim(sprintf('composer %sremove %s', $global, $name))
            . $this->mapOptions($options)
        );
    }

    /**
     * @return string
     */
    public function getHomePath()
    {
        return $this->getExecutor()->read('composer config home --absolute');
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
        return $this->getComposerConfigManager($cwd)->set($config);
    }

    /**
     * @param string $cwd
     * @return int
     */
    public function flushConfig($cwd = '')
    {
        return $this->getComposerConfigManager($cwd)->flush();
    }

    /**
     * @param string $cwd
     * @return int
     */
    public function validateConfig($cwd = '')
    {
        return $this->execute('composer validate', $cwd);
    }

    /**
     * @param string $cwd
     * @return int
     */
    public function dumpAutoload($cwd = '')
    {
        return $this->execute('composer dump-autoload', $cwd);
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
            $this->composerConfigManager[$cwd] = $this->getBalloonFactory()->create($this->getConfigPath($cwd));
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

    /**
     * Getter of $balloonFactory
     *
     * @return BalloonFactory
     */
    private function getBalloonFactory()
    {
        return $this->balloonFactory;
    }

    /**
     * Setter of $balloonFactory
     *
     * @param BalloonFactory $balloonFactory
     */
    private function setBalloonFactory(BalloonFactory $balloonFactory)
    {
        $this->balloonFactory = $balloonFactory;
    }

    /**
     * @param string $command
     * @param string $cwd
     * @return int
     */
    private function execute($command, $cwd = null)
    {
        $pipes = [];
        return $this->getExecutor()->flush($command, [STDIN, STDOUT, STDERR], $pipes, $cwd);
    }
}
