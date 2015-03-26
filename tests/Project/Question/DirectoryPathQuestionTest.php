<?php
namespace Samurai\Project\Question;

use Pimple\Container;
use Samurai\Project\Composer\Composer;
use Samurai\Project\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use TRex\Cli\Executor;

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
        $services['composer']->getproject()->setName('vendor/package');

        $question = new DirectoryPathQuestion($services);
        $this->assertTrue($question->execute($input, $output));
        $this->assertSame('vendor/package', $services['composer']->getproject()->getDirectoryPath());
    }

    public function testExecuteValid()
    {
        $input = $this->provideInput(['--dir' => 'dir/path']);
        $output = new BufferedOutput();
        $services = $this->provideServices();
        $services['composer']->getproject()->setName('vendor/package');

        $question = new DirectoryPathQuestion($services);
        $this->assertTrue($question->execute($input, $output));
        $this->assertSame('dir/path', $services['composer']->getproject()->getDirectoryPath());
    }

    public function testExecuteEmpty()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices();

        $question = new DirectoryPathQuestion($services);
        $this->assertFalse($question->execute($input, $output));
        $this->assertSame('', $services['composer']->getproject()->getDirectoryPath());
    }

    /**
     * @return Container
     */
    private function provideServices()
    {
        $services = new Container();
        $services['composer'] = function () {
            return new Composer(new Project(), new Executor());
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

