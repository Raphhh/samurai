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
 * Class KeywordsQuestionTest
 * @package Samurai\Composer\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class KeywordsQuestionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelper($input, $output));

        $this->assertNull($services['composer']->getProject()->getKeywords());

        $question = new KeywordsQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame(['k1', 'k2'], $services['composer']->getProject()->getKeywords());
    }

    public function testExecuteEmpty()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices($this->provideQuestionHelperEmpty($input, $output));

        $this->assertNull($services['composer']->getProject()->getKeywords());

        $question = new KeywordsQuestion($services);
        $this->assertTrue($question->execute($input, $output));

        $this->assertSame([], $services['composer']->getProject()->getKeywords());
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
