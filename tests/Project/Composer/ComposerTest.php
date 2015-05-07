<?php
namespace Samurai\Project\Composer;

use Balloon\Factory\BalloonFactory;
use Balloon\Reader\Factory\DummyFileReaderFactory;
use Samurai\Alias\Alias;
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
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));

        $this->setExpectedException('\InvalidArgumentException', 'The bootstrap of the project is not defined');
        $composer->createProject($project);
    }

    public function testCreateProjectWithBootstrap()
    {
        $alias = new Alias();
        $alias->setPackage('vendor/package');

        $project = new Project();
        $project->setBootstrap($alias);

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package')
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->createProject($project));
    }

    public function testCreateProjectWithBootstrapAndOptions()
    {
        $alias = new Alias();
        $alias->setPackage('vendor/package');

        $project = new Project();
        $project->setBootstrap($alias);

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package --repository-url=url')
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->createProject($project, ['repository-url' => 'url']));
    }

    public function testCreateProjectWithDirectoryPath()
    {
        $alias = new Alias();
        $alias->setPackage('vendor/package');

        $project = new Project();
        $project->setBootstrap($alias);
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path')
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->createProject($project));
    }

    public function testCreateProjectWithDirectoryPathAndOptions()
    {
        $alias = new Alias();
        $alias->setPackage('vendor/package');

        $project = new Project();
        $project->setBootstrap($alias);
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path --repository-url=url')
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->createProject($project, ['repository-url' => 'url']));
    }

    public function testCreateProjectWithVersion()
    {
        $alias = new Alias();
        $alias->setPackage('vendor/package');
        $alias->setVersion('1.0.0');

        $project = new Project();
        $project->setBootstrap($alias);
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path 1.0.0')
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->createProject($project));
    }

    public function testCreateProjectWithVersionAndOptions()
    {
        $alias = new Alias();
        $alias->setPackage('vendor/package');
        $alias->setVersion('1.0.0');

        $project = new Project();
        $project->setBootstrap($alias);
        $project->setDirectoryPath('dir/path');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with('composer create-project --prefer-dist vendor/package dir/path 1.0.0 --repository-url=url')
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->createProject($project, ['repository-url' => 'url']));
    }

    public function testGetConfigPath()
    {
        $project = new Project();
        $composer = new Composer(new Executor(), new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame('composer.json', $composer->getConfigPath($project->getDirectoryPath()));
    }

    public function testGetConfigPathWithDirectoryPath()
    {
        $project = new Project();
        $project->setDirectoryPath('dir/path');
        $composer = new Composer(new Executor(), new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame('dir/path/composer.json', $composer->getConfigPath($project->getDirectoryPath()));
    }

    public function testGetConfigWithoutFile()
    {
        $project = new Project();
        $project->setDirectoryPath('no-such-dir');
        $composer = new Composer(new Executor(), new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame([], $composer->getConfig($project->getDirectoryPath()), 'config path: ' . $composer->getConfigPath($project->getDirectoryPath()));
    }

    public function testGetConfigWithFile()
    {
        $project = new Project();
        $project->setDirectoryPath(__DIR__ . '/../resources');
        $composer = new Composer(new Executor(), new BalloonFactory());
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
            ->will($this->returnValue(1));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(1, $composer->validateConfig($project->getDirectoryPath()));
    }

    public function testValidateConfigWithFile()
    {
        $project = new Project();
        $project->setDirectoryPath(__DIR__ . '/../resources');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with(
                'composer validate',
                [STDIN, STDOUT, STDERR],
                [],
                __DIR__ . '/../resources'
            )
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->validateConfig($project->getDirectoryPath()));
    }

    public function testDumpAutoload()
    {
        $project = new Project();
        $project->setDirectoryPath(__DIR__ . '/../resources');

        $executor = $this->getMockBuilder('TRex\Cli\Executor')->getMock();
        $executor->expects($this->once())
            ->method('flush')
            ->with(
                'composer dump-autoload',
                [STDIN, STDOUT, STDERR],
                [],
                __DIR__ . '/../resources'
            )
            ->will($this->returnValue(0));

        $composer = new Composer($executor, new BalloonFactory(new DummyFileReaderFactory()));
        $this->assertSame(0, $composer->dumpAutoload($project->getDirectoryPath()));
    }
}
