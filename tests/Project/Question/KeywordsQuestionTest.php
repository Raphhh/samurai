<?php
namespace Samurai\Project\Question;

use Pimple\Container;
use Samurai\Project\Project;
use Samurai\Task\ITask;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Question\Question;

/**
 * Class KeywordsQuestionTest
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class KeywordsQuestionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelper($input, $output));

        $this->assertNull($services['project']->getKeywords());

        $question = new KeywordsQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));

        $this->assertSame(['k1', 'k2'], $services['project']->getKeywords());
    }

    public function testExecuteEmpty()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperEmpty($input, $output));

        $this->assertNull($services['project']->getKeywords());

        $question = new KeywordsQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));

        $this->assertSame([], $services['project']->getKeywords());
    }

    /**
     * @param $questionHelper
     * @return Container
     */
    private function provideServices($questionHelper)
    {
        $services = new Container();
        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        $services['project'] = function () {
            return new Project();
        };
        return $services;
    }

    /**
     * @param $input
     * @param $output
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideQuestionHelper($input, $output)
    {
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->callback(
                    function (Question $question) {
                        return '<question>Enter your project keywords (comma separated):</question>' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue(' k1 , k2 '));
        return $questionHelper;
    }

    /**
     * @param $input
     * @param $output
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideQuestionHelperEmpty($input, $output)
    {
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->callback(
                    function (Question $question) {
                        return '<question>Enter your project keywords (comma separated):</question>' === $question->getQuestion();
                    }
                )
            )
            ->will($this->returnValue(''));
        return $questionHelper;
    }
}
