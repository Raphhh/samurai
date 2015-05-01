<?php
namespace Samurai\Project\Composer;

use Samurai\Project\Project;
use TRex\Cli\Executor;

/**
 * Class ComposerTest
 * @package Samurai\Project\Composer
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ComposerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProject()
    {
        $project = new Project();

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->never())
            ->method('flush')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);

        $this->setExpectedException('\InvalidArgumentException', 'The bootstrap of the project is not defined');
        $composer->createProject($project);
    }

    public function testCreateProjectWithBootstrap()
    {
        $project = new Project();
        $project->setBootstrapName('vendor/package');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);
        $this->assertSame('result', $composer->createProject($project));
    }

    public function testCreateProjectWithBootstrapAndOptions()
    {
        $project = new Project();
        $project->setBootstrapName('vendor/package');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package --repository-url=url')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);
        $this->assertSame('result', $composer->createProject($project, ['repository-url' => 'url']));
    }

    public function testCreateProjectWithDirectoryPath()
    {
        $project = new Project();
        $project->setBootstrapName('vendor/package');
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);
        $this->assertSame('result', $composer->createProject($project));
    }

    public function testCreateProjectWithDirectoryPathAndOptions()
    {
        $project = new Project();
        $project->setBootstrapName('vendor/package');
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path --repository-url=url')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);
        $this->assertSame('result', $composer->createProject($project, ['repository-url' => 'url']));
    }

    public function testCreateProjectWithVersion()
    {
        $project = new Project();
        $project->setBootstrapName('vendor/package');
        $project->setBootstrapVersion('1.0.0');
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path 1.0.0')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);
        $this->assertSame('result', $composer->createProject($project));
    }

    public function testCreateProjectWithVersionAndOptions()
    {
        $project = new Project();
        $project->setBootstrapName('vendor/package');
        $project->setBootstrapVersion('1.0.0');
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path 1.0.0 --repository-url=url')
            ->will($this->returnValue('result'));

        $composer = new Composer($executor);
        $this->assertSame('result', $composer->createProject($project, ['repository-url' => 'url']));
    }

    public function testGetConfigPath()
    {
        $project = new Project();
        $composer = new Composer(new Executor());
        $this->assertSame('composer.json', $composer->getConfigPath($project->getDirectoryPath()));
    }

    public function testGetConfigPathWithDirectoryPath()
    {
        $project = new Project();
        $project->setDirectoryPath('dir/path');
        $composer = new Composer(new Executor());
        $this->assertSame('dir/path/composer.json', $composer->getConfigPath($project->getDirectoryPath()));
    }

    public function testGetConfigWithoutFile()
    {
        $project = new Project();
        $project->setDirectoryPath('no-such-dir');
        $composer = new Composer(new Executor());
        $this->assertNull($composer->getConfig($project->getDirectoryPath()), 'config path: ' . $composer->getConfigPath($project->getDirectoryPath()));
    }

    public function testGetConfigWithFile()
    {
        $project = new Project();
        $project->setDirectoryPath(__DIR__ . '/../resources');
        $composer = new Composer(new Executor());
        $this->assertSame(
            [
                'name' => 'raphhh/samurai',
                'description' => 'desc',
                'license' => 'MIT',
                'version' => '1.0.0',
                'time' => '1999-12-31',
            ],
            $composer->getConfig($project->getDirectoryPath())
        );
    }

    public function testValidateConfigWithoutFile()
    {
        $project = new Project();

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer validate')
            ->will($this->returnValue(false));

        $composer = new Composer($executor);
        $this->assertFalse($composer->validateConfig($project->getDirectoryPath()));
    }

    public function testValidateConfigWithFile()
    {
        $project = new Project();
        $project->setDirectoryPath(__DIR__ . '/../resources');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('cd '.__DIR__ .'/../resources && composer validate')
            ->will($this->returnValue(true));

        $composer = new Composer($executor);
        $this->assertTrue($composer->validateConfig($project->getDirectoryPath()));
    }

    public function testDumpAutoload()
    {
        $project = new Project();
        $project->setDirectoryPath(__DIR__ . '/../resources');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('cd '.__DIR__ .'/../resources && composer dump-autoload')
            ->will($this->returnValue(true));

        $composer = new Composer($executor);
        $this->assertTrue($composer->dumpAutoload($project->getDirectoryPath()));
    }
}
