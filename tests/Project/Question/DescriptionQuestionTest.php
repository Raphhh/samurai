<?php
namespace Samurai\Project\Question;

use Pimple\Container;
use Samurai\Project\Composer\Composer;
use Samurai\Project\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TRex\Cli\Executor;

/**
 * Class DescriptionQuestionTest
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class DescriptionQuestionTest extends \PHPUnit_Framework_TestCase
{

    public function testExecute()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($input, $output);

        $this->assertNull($services['composer']->getProject()->getDescription());

        $question = new DescriptionQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame('result', $services['composer']->getProject()->getDescription());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Container
     */
    private function provideServices(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->provideQuestionHelper($input, $output);

        $services = new Container();
        $services['question'] = function () use ($questionHelper) {
            return $questionHelper;
        };

        $services['composer'] = function () {
            return new Composer(new Project(), new Executor());
        };

        return $services;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    private function provideQuestionHelper(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->callback(
                    function (Question $question) {
                        return '<question>Enter your project description:</question>' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue('result'));

        return $questionHelper;
    }
}
