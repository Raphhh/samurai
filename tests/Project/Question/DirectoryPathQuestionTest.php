<?php
namespace Samurai\Project\Question;

use Pimple\Container;
use Samurai\Project\Project;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class DirectoryPathQuestionTest
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DirectoryPathQuestionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteWithDefaultName()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices();
        $services['project']->setName('vendor/package');

        $question = new DirectoryPathQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));
        $this->assertSame(getcwd() . DIRECTORY_SEPARATOR . 'vendor/package', $services['project']->getDirectoryPath());
    }

    public function testExecuteValid()
    {
        $input = $this->provideInput(['--dir' => 'dir/path']);
        $output = new BufferedOutput();
        $services = $this->provideServices();
        $services['project']->setName('vendor/package');

        $question = new DirectoryPathQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));
        $this->assertSame(getcwd() . DIRECTORY_SEPARATOR . 'dir/path', $services['project']->getDirectoryPath());
    }

    public function testExecuteEmpty()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices();

        $question = new DirectoryPathQuestion($services);
        $this->assertSame(ITask::BLOCKING_ERROR_CODE, $question->execute($input, $output));
        $this->assertNull($services['project']->getDirectoryPath());
    }

    /**
     * @return Container
     */
    private function provideServices()
    {
        $services = new Container();
        $services['project'] = function () {
            return new Project();
        };
        return $services;
    }

    /**
     * @param array $args
     * @return ArrayInput
     */
    private function provideInput(array $args)
    {
        return new ArrayInput(
            $args,
            new InputDefinition([
                new InputOption('dir'),
            ])
        );
    }
}

