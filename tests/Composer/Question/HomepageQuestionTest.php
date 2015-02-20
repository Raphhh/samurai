<?php
namespace Samurai\Composer\Question;

use Pimple\Container;
use Samurai\Composer\Composer;
use Samurai\Composer\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Question\Question;
use TRex\Cli\Executor;

/**
 * Class HomepageQuestionTest
 * @package Samurai\Composer\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class HomepageQuestionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteValid()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperValid($input, $output));

        $this->assertNull($services['composer']->getProject()->getHomepage());

        $question = new HomepageQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame('http://website.com', $services['composer']->getProject()->getHomepage());
    }

    public function testExecuteEmpty()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $services = $this->provideServices($this->provideQuestionHelperEmpty($input, $output));

        $this->assertNull($services['composer']->getProject()->getHomepage());

        $question = new HomepageQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame('', $services['composer']->getProject()->getHomepage());
    }

    public function testExecuteNotValid()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperNotValid($input, $output));

        $this->assertNull($services['composer']->getProject()->getHomepage());

        $question = new HomepageQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame('', $services['composer']->getProject()->getHomepage());
    }

    /**
     * @param $questionHelper
     * @return Container
     */
    private function provideServices($questionHelper)
    {
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
