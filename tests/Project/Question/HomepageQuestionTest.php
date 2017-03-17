<?php
namespace Samurai\Project\Question;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Project\Project;
use Samurai\Task\ITask;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Question\Question;

/**
 * Class HomepageQuestionTest
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class HomepageQuestionTest extends TestCase
{
    public function testExecuteValid()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperValid($input, $output));

        $this->assertNull($services['project']->getHomepage());

        $question = new HomepageQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));

        $this->assertSame('http://website.com', $services['project']->getHomepage());
    }

    public function testExecuteEmpty()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices($this->provideQuestionHelperEmpty($input, $output));

        $this->assertNull($services['project']->getHomepage());

        $question = new HomepageQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));

        $this->assertSame('', $services['project']->getHomepage());
    }

    public function testExecuteNotValid()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperNotValid($input, $output));

        $this->assertNull($services['project']->getHomepage());

        $question = new HomepageQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));

        $this->assertSame('', $services['project']->getHomepage());
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
    private function provideQuestionHelperValid($input, $output)
    {
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->callback(
                    function (Question $question) {
                        $validator = $question->getValidator();
                        return $question->getQuestion() === '<question>Enter your project homepage:</question>'
                        && $question->getMaxAttempts() === 3
                        && $validator('http://website.com') === 'http://website.com';
                    }
                )
            )
            ->will($this->returnValue('http://website.com'));
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
                        $validator = $question->getValidator();
                        return $question->getQuestion() === '<question>Enter your project homepage:</question>'
                        && $question->getMaxAttempts() === 3
                        && $validator('') === '';
                    }
                )
            )
            ->will($this->returnValue(''));
        return $questionHelper;
    }

    /**
     * @param $input
     * @param $output
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function provideQuestionHelperNotValid($input, $output)
    {
        $questionHelper = $this->getMockBuilder('Symfony\Component\Console\Helper\QuestionHelper')->getMock();

        $questionHelper->expects($this->once())
            ->method('ask')
            ->with(
                $input,
                $output,
                $this->callback(
                    function (Question $question) {
                        $validator = $question->getValidator();
                        try {
                            $validator('foo');
                            return false;
                        } catch (\RuntimeException $e) {
                            return $e->getMessage() === 'Error: format not valid';
                        }
                    }
                )
            )
            ->will($this->throwException(new \RuntimeException('Error: format not valid')));
        return $questionHelper;
    }
}
