<?php
namespace Samurai\Project\Question;

use Pimple\Container;
use Samurai\Project\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $services = $this->provideServices($input, $output, 'result');

        $this->assertNull($services['project']->getDescription());

        $question = new DescriptionQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame('result', $services['project']->getDescription());
    }

    public function testExecuteEmpty()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($input, $output, '');

        $this->assertNull($services['project']->getDescription());

        $question = new DescriptionQuestion($services);
        $this->assertFalse($question->execute($input, $output));

        $this->assertSame('', $services['project']->getDescription());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $result
     * @return Container
     */
    private function provideServices(InputInterface $input, OutputInterface $output, $result)
    {
        $questionHelper = $this->provideQuestionHelper($input, $output, $result);

        $services = new Container();
        $services['question'] = function () use ($questionHelper) {
            return $questionHelper;
        };

        $services['project'] = function () {
            return new Project();
        };

        return $services;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $result
     * @return \Symfony\Component\Console\Helper\QuestionHelper
     */
    private function provideQuestionHelper(InputInterface $input, OutputInterface $output, $result)
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
            ->will($this->returnValue($result));

        return $questionHelper;
    }
}
