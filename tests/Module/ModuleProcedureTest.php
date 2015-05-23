<?php
namespace Samurai\Module;

use Balloon\Factory\BalloonFactory;
use Balloon\Reader\DummyFileReader;
use Balloon\Reader\Factory\DummyFileReaderFactory;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ModuleProcedureTest
 * @package Samurai\Module\Task
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class ModuleProcedureTest extends \PHPUnit_Framework_TestCase
{

    public function testRemove()
    {
        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = new BalloonFactory(new DummyFileReaderFactory(new DummyFileReader()));
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $modules = new Modules();

        $module = new Module();
        $module->setPackage('none/none');
        $module->setName('a');

        $composer->expects($this->once())
            ->method('removePackage')
            ->with('none/none', true)
            ->will($this->returnValue(0));

        $moduleManager->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($module->getName()));

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($modules));

        $modulesSorter->expects($this->once())
            ->method('sort')
            ->with($this->identicalTo($modules))
            ->will($this->returnValue($modules));

        $output = new BufferedOutput();

        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertTrue($moduleProcedure->remove($module));
        $this->assertSame(
            "Removing none/none.\nSorting modules.\n",
            $output->fetch()
        );
    }

    public function testRemoveWithComposerFail()
    {
        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = new BalloonFactory(new DummyFileReaderFactory(new DummyFileReader()));
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $modules = new Modules();

        $module = new Module();
        $module->setPackage('none/none');
        $module->setName('a');

        $composer->expects($this->once())
            ->method('removePackage')
            ->with('none/none', true)
            ->will($this->returnValue(1));

        $moduleManager->expects($this->never())
            ->method('remove');

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($modules));

        $modulesSorter->expects($this->never())
            ->method('sort');

        $output = new BufferedOutput();

        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertFalse($moduleProcedure->remove($module));
        $this->assertSame(
            "Removing none/none.\nAn error occurred during the remove of none/none.\n",
            $output->fetch()
        );
    }

    public function testRemoveWithDependents()
    {
        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = new BalloonFactory(new DummyFileReaderFactory(new DummyFileReader()));
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $modules = new Modules();

        $module = new Module();
        $module->setPackage('none/none');
        $module->setName('a');

        $dependent = new Module();
        $dependent->setPackage('p/d');
        $dependent->setDependencies(new Modules([$module]));
        $modules['d'] = $dependent;

        $composer->expects($this->never())
            ->method('removePackage');

        $moduleManager->expects($this->never())
            ->method('remove');

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($modules));

        $modulesSorter->expects($this->never())
            ->method('sort');

        $output = new BufferedOutput();

        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertFalse($moduleProcedure->remove($module));
        $this->assertSame(
            "Removing none/none.\nThe module \"a\" cant not be removed because is a dependency of \"d\". First remove \"d\".\n",
            $output->fetch()
        );
    }

    public function testImportWithIncorrectModulePackage()
    {
        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = new BalloonFactory(new DummyFileReaderFactory(new DummyFileReader()));
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $composer->expects($this->once())
            ->method('requirePackage')
            ->with('none/none', '@dev', true)
            ->will($this->returnValue(1));

        $module = new Module();
        $module->setPackage('none/none');
        $module->setVersion('@dev');
        $module->setIsEnable(true);

        $output = new BufferedOutput();

        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertFalse($moduleProcedure->import($module));
        $this->assertSame(
            "Starting installation of none/none.\nAn error occurred during the installation of none/none.\n",
            $output->fetch()
        );
    }

    public function testImportWithCorrectModulePackage()
    {
        $data = new DummyFileReader();
        $data->write('{
          "package": "raphhh/samurai-module-git",
          "description": "Git module",
          "dependencies": [

          ],
          "tasks": [
            "Samurai\\\Git\\\GitTask"
          ]
        }');

        $module = new Module();
        $module->setName('name');
        $module->setPackage('none/none');
        $module->setVersion('@dev');
        $module->setIsEnable(true);

        $modules = new Modules();

        $output = new BufferedOutput();

        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = $this->getMockBuilder('Balloon\Factory\BalloonFactory')->disableOriginalConstructor()->getMock();
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $balloonFactory->expects($this->at(0))
            ->method('create')
            ->with('COMPOSER_HOME/vendor/none/none/.samurai.json')
            ->will($this->returnValue((new BalloonFactory(new DummyFileReaderFactory($data)))->create('.json')));

        $composer->expects($this->once())
            ->method('requirePackage')
            ->with('none/none', '@dev', true)
            ->will($this->returnValue(0));

        $composer->expects($this->once())
            ->method('getHomePath')
            ->will($this->returnValue('COMPOSER_HOME'));


        $moduleManager->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($module));

        $moduleManager->expects($this->once())
            ->method('set')
            ->with($this->identicalTo($modules));

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($modules));

        $modulesSorter->expects($this->once())
            ->method('sort')
            ->with($this->identicalTo($modules))
            ->will($this->returnValue($modules));



        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertTrue($moduleProcedure->import($module));

        $this->assertSame(
            "Starting installation of none/none.\nSorting modules.\n",
            $output->fetch()
        );

        $this->assertSame(
            [
                'name' => 'name',
                'description' => 'Git module', //overridden
                'package' => 'none/none',
                'version' => '@dev',
                'source' => null,
                'isEnable' => true,
                'tasks' => [
                    'Samurai\Git\GitTask'
                ],
                'dependencies' => [],
            ],
            $module->toArray()
        );
    }

    public function testImportWithDependencies()
    {
        $data = new DummyFileReader();
        $data->write('{
          "package": "raphhh/samurai-module-git",
          "description": "Git module",
          "dependencies": [
                {
                    "package": "dependency/1",
                    "version": "1"
                },
                {
                    "package": "dependency/2",
                    "version": "2",
                    "dependencies": [
                        {
                            "package": "dependency/1",
                            "version": "1"
                        }
                  ]
                }
          ],
          "tasks": [
            "Samurai\\\Git\\\GitTask"
          ]
        }');

        $dependency1Data = new DummyFileReader();
        $dependency1Data->write('{
          "package": "dependency/1",
          "description": "dependency 1",
          "dependencies": [
          ],
          "tasks": [
          ]
        }');

        $dependency2Data = new DummyFileReader();
        $dependency2Data->write('{
          "package": "dependency/2",
          "description": "dependency 2",
          "dependencies": [
          ],
          "tasks": [
          ]
        }');

        $module = new Module();
        $module->setName('name');
        $module->setPackage('none/none');
        $module->setVersion('@dev');
        $module->setIsEnable(true);

        $modules = new Modules();

        $output = new BufferedOutput();

        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = $this->getMockBuilder('Balloon\Factory\BalloonFactory')->disableOriginalConstructor()->getMock();
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $balloonFactory->expects($this->at(0))
            ->method('create')
            ->with('COMPOSER_HOME/vendor/none/none/.samurai.json')
            ->will($this->returnValue((new BalloonFactory(new DummyFileReaderFactory($data)))->create('.json')));

        $balloonFactory->expects($this->at(1))
            ->method('create')
            ->with('COMPOSER_HOME/vendor/dependency/1/.samurai.json')
            ->will($this->returnValue((new BalloonFactory(new DummyFileReaderFactory($dependency1Data)))->create('.json')));

        $balloonFactory->expects($this->at(2))
            ->method('create')
            ->with('COMPOSER_HOME/vendor/dependency/2/.samurai.json')
            ->will($this->returnValue((new BalloonFactory(new DummyFileReaderFactory($dependency2Data)))->create('.json')));

//        $composer->expects($this->exactly(3))
//            ->method('requirePackage')
//            ->will($this->returnValue(0));

        $composer->expects($this->at(0))
            ->method('requirePackage')
            ->with('none/none', '@dev', true)
            ->will($this->returnValue(0));

        $composer->expects($this->at(2)) //todo why not 1? could be a phpunit bug???
        ->method('requirePackage')
            ->with('dependency/1', '1', true)
            ->will($this->returnValue(0));

        $composer->expects($this->at(4))//todo why not 2? could be a phpunit bug???
        ->method('requirePackage')
            ->with('dependency/2', '2', true)
            ->will($this->returnValue(0));

        $composer->expects($this->exactly(3))
            ->method('getHomePath')
            ->will($this->returnValue('COMPOSER_HOME'));


        $moduleManager->expects($this->exactly(3))
            ->method('add');

        $moduleManager->expects($this->once())
            ->method('set')
            ->with($this->identicalTo($modules));

        $moduleManager->expects($this->never())
            ->method('clear');

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($modules));

        $modulesSorter->expects($this->once())
            ->method('sort')
            ->with($this->identicalTo($modules))
            ->will($this->returnValue($modules));



        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertTrue($moduleProcedure->import($module));

        $this->assertSame(
            "Starting installation of none/none.\nStarting installation of dependency/1.\nStarting installation of dependency/2.\nSorting modules.\n",
            $output->fetch()
        );

        $this->assertSame(
            [
                'name' => 'name',
                'description' => 'Git module', //overridden
                'package' => 'none/none',
                'version' => '@dev',
                'source' => null,
                'isEnable' => true,
                'tasks' => [
                    'Samurai\Git\GitTask'
                ],
                'dependencies' => [
                    [
                        'name' => '',
                        'description' => 'dependency 1',
                        'package' => 'dependency/1',
                        'version' => '1',
                        'source' => '',
                        'isEnable' => true,
                        'tasks' => [],
                        'dependencies' => [],
                    ],
                    [
                        'name' => '',
                        'description' => 'dependency 2',
                        'package' => 'dependency/2',
                        'version' => '2',
                        'source' => '',
                        'isEnable' => true,
                        'tasks' =>[],
                        'dependencies' => [],
                    ],
                ],
            ],
            $module->toArray()
        );
    }

    public function testImportWithDependenciesButComposerError()
    {
        $data = new DummyFileReader();
        $data->write('{
          "package": "raphhh/samurai-module-git",
          "description": "Git module",
          "dependencies": [
                {
                    "package": "dependency/1",
                    "version": "1"
                },
                {
                    "package": "dependency/2",
                    "version": "2",
                    "dependencies": [
                        {
                            "package": "dependency/1",
                            "version": "1"
                        }
                  ]
                }
          ],
          "tasks": [
            "Samurai\\\Git\\\GitTask"
          ]
        }');

        $dependency1Data = new DummyFileReader();
        $dependency1Data->write('{
          "package": "dependency/1",
          "description": "dependency 1",
          "dependencies": [
          ],
          "tasks": [
          ]
        }');

        $dependency2Data = new DummyFileReader();
        $dependency2Data->write('{
          "package": "dependency/2",
          "description": "dependency 2",
          "dependencies": [
          ],
          "tasks": [
          ]
        }');

        $module = new Module();
        $module->setName('name');
        $module->setPackage('none/none');
        $module->setVersion('@dev');
        $module->setIsEnable(true);

        $modules = new Modules();

        $output = new BufferedOutput();

        $moduleManager = $this->getMockBuilder('Samurai\Module\ModuleManager')->disableOriginalConstructor()->getMock();
        $composer = $this->getMockBuilder('Samurai\Project\Composer\Composer')->disableOriginalConstructor()->getMock();
        $balloonFactory = $this->getMockBuilder('Balloon\Factory\BalloonFactory')->disableOriginalConstructor()->getMock();
        $modulesSorter = $this->getMockBuilder('Samurai\Module\ModulesSorter')->disableOriginalConstructor()->getMock();

        $balloonFactory->expects($this->at(0))
            ->method('create')
            ->with('COMPOSER_HOME/vendor/none/none/.samurai.json')
            ->will($this->returnValue((new BalloonFactory(new DummyFileReaderFactory($data)))->create('.json')));

        $composer->expects($this->at(0))
            ->method('requirePackage')
            ->with('none/none', '@dev', true)
            ->will($this->returnValue(0));

        $composer->expects($this->at(2)) //todo why not 1? could be a phpunit bug???
        ->method('requirePackage')
            ->with('dependency/1', '1', true)
            ->will($this->returnValue(1)); //error

        $composer->expects($this->exactly(1))
            ->method('getHomePath')
            ->will($this->returnValue('COMPOSER_HOME'));


        $moduleManager->expects($this->exactly(1))
            ->method('add');

        $moduleManager->expects($this->never())
            ->method('set')
            ->with($this->identicalTo($modules));

        $moduleManager->expects($this->once())
            ->method('clear');

        $moduleManager->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($modules));

        $modulesSorter->expects($this->never())
            ->method('sort');


        $moduleProcedure = new ModuleProcedure($moduleManager, $composer, $balloonFactory, $modulesSorter);
        $moduleProcedure->setOutput($output);
        $this->assertFalse($moduleProcedure->import($module));

        $this->assertSame(
            "Starting installation of none/none.\nStarting installation of dependency/1.\nAn error occurred during the installation of dependency/1.\nRoll-backing installation of none/none.\n",
            $output->fetch()
        );

        $this->assertSame(
            [
                'name' => 'name',
                'description' => 'Git module', //overridden
                'package' => 'none/none',
                'version' => '@dev',
                'source' => null,
                'isEnable' => true,
                'tasks' => [
                    'Samurai\Git\GitTask'
                ],
                'dependencies' => [
                    [
                        'name' => '',
                        'description' => '',
                        'package' => 'dependency/1',
                        'version' => '1',
                        'source' => '',
                        'isEnable' => true,
                        'tasks' => [],
                        'dependencies' => [],
                    ],
                    [
                        'name' => '',
                        'description' => '',
                        'package' => 'dependency/2',
                        'version' => '2',
                        'source' => '',
                        'isEnable' => true,
                        'tasks' =>[],
                        'dependencies' => [],
                    ],
                ],
            ],
            $module->toArray()
        );
    }
}
