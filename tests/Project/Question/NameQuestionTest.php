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
 * Class NameQuestionTest
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class NameQuestionTest extends TestCase
{

    public function testExecuteValid()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperValid($input, $output));

        $this->assertNull($services['project']->getName());

        $question = new NameQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));

        $this->assertSame('vendor/package', $services['project']->getName());
    }

    public function testExecuteEmpty()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperEmpty($input, $output));

        $this->assertNull($services['project']->getName());

        $question = new NameQuestion($services);
        $this->setExpectedException('RuntimeException', 'Error: format not valid');
        $this->assertFalse($question->execute($input, $output));

        $this->assertSame('', $services['project']->getName());
    }

    public function testExecuteNotValid()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperNotValid($input, $output));

        $this->assertNull($services['project']->getName());

        $question = new NameQuestion($services);
        $this->setExpectedException('RuntimeException', 'Error: format not valid');
        $this->assertFalse($question->execute($input, $output));

        $this->assertSame('', $services['project']->getName());
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
                        return $question->getQuestion() === '<question>Enter your project name (<vendor>/<package>):</question>'
                        && $question->getMaxAttempts() === 3
                        && $validator('vendor/package') === 'vendor/package';
                    }
                )
            )
            ->will($this->returnValue('vendor/package'));
        return $questionHelper;
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
                        try {
                            $validator('');
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
                            $validator('vendor');
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
