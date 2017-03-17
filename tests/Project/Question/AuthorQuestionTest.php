<?php
namespace Samurai\Project\Question;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Samurai\Project\Author;
use Samurai\Project\Project;
use Samurai\Task\ITask;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class AuthorQuestionTest
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AuthorQuestionTest extends TestCase
{
    public function testExecuteForOneAuthorWithGit()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServicesForOneAuthorWithGit();

        $question = new AuthorQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));
        $authors = $services['project']->getAuthors();
        $this->assertCount(1, $authors);
        $this->assertSame('git.name', $authors[0]->getName());
        $this->assertSame('git.email@mail.com', $authors[0]->getEmail());
    }

    /**
     * @return Container
     */
    private function provideServicesForOneAuthorWithGit()
    {
        $services = new Container();
        $services['project'] = function () {
            return new Project();
        };

        $git = $this->getMock('PHPGit\Git', array('config'));
        $git->expects($this->once())
            ->method('config')
            ->will($this->returnValue([
                'user.name' => 'git.name',
                'user.email' => 'git.email@mail.com',
            ]));


        $services['git'] = function () use($git){
            return $git;
        };

        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Do you confirm this author "git.name <git.email@mail.com>"?[y]</question>',
                                $question->getQuestion()
                            );
                            return true;
                        },
                        $this
                    )
                )
            );

        $questionHelper->expects($this->at(1))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Do you want to add another author?[n]</question>',
                                $question->getQuestion()
                            );
                            return false;
                        },
                        $this
                    )
                )
            );

        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        return $services;
    }

    public function testExecuteForTwoAuthorsWithGit()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServicesForTwoAuthorsWithGit();

        $question = new AuthorQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));
        $authors = $services['project']->getAuthors();
        $this->assertCount(2, $authors);
        $this->assertSame('git.name', $authors[0]->getName());
        $this->assertSame('git.email@mail.com', $authors[0]->getEmail());
        $this->assertSame('add.name', $authors[1]->getName());
        $this->assertSame('add.email@mail.com', $authors[1]->getEmail());
    }

    /**
     * @return Container
     */
    private function provideServicesForTwoAuthorsWithGit()
    {
        $services = new Container();
        $services['project'] = function () {
            return new Project();
        };

        $git = $this->getMock('PHPGit\Git', array('config'));
        $git->expects($this->once())
            ->method('config')
            ->will($this->returnValue([
                'user.name' => 'git.name',
                'user.email' => 'git.email@mail.com',
            ]));


        $services['git'] = function () use($git){
            return $git;
        };

        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Do you confirm this author "git.name <git.email@mail.com>"?[y]</question>',
                                $question->getQuestion()
                            );
                            return true;
                        },
                        $this
                    )
                )
            );

        $questionHelper->expects($this->at(1))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Do you want to add another author?[n]</question>',
                                $question->getQuestion()
                            );
                            return true;
                        },
                        $this
                    )
                )
            );

        $questionHelper->expects($this->at(2))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Enter the author (name <mail@mail.com>):</question>',
                                $question->getQuestion()
                            );
                            return new Author('add.name <add.email@mail.com>');
                        },
                        $this
                    )
                )
            );

        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        return $services;
    }

    public function testExecuteForTwoAuthorsWithoutGit()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServicesForTwoAuthorsWithoutGit();

        $question = new AuthorQuestion($services);
        $this->assertSame(ITask::NO_ERROR_CODE, $question->execute($input, $output));
        $authors = $services['project']->getAuthors();
        $this->assertCount(2, $authors);
        $this->assertSame('main.name', $authors[0]->getName());
        $this->assertSame('main.email@mail.com', $authors[0]->getEmail());
        $this->assertSame('add.name', $authors[1]->getName());
        $this->assertSame('add.email@mail.com', $authors[1]->getEmail());
    }

    /**
     * @return Container
     */
    private function provideServicesForTwoAuthorsWithoutGit()
    {
        $services = new Container();
        $services['project'] = function () {
            return new Project();
        };

        $git = $this->getMock('PHPGit\Git', array('config'));
        $git->expects($this->once())
            ->method('config')
            ->will($this->returnValue([]));


        $services['git'] = function () use($git){
            return $git;
        };

        $questionHelper = $this->getMock('Symfony\Component\Console\Helper\QuestionHelper', array('ask'));
        $questionHelper->expects($this->at(0))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Enter the author (name <mail@mail.com>):</question>',
                                $question->getQuestion()
                            );
                            return new Author('main.name <main.email@mail.com>');
                        },
                        $this
                    )
                )
            );

        $questionHelper->expects($this->at(1))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Do you want to add another author?[n]</question>',
                                $question->getQuestion()
                            );
                            return true;
                        },
                        $this
                    )
                )
            );

        $questionHelper->expects($this->at(2))
            ->method('ask')
            ->will(
                $this->returnCallback(
                    \Closure::bind(
                        function(InputInterface $input, OutputInterface $output, Question $question){
                            $this->assertSame(
                                '<question>Enter the author (name <mail@mail.com>):</question>',
                                $question->getQuestion()
                            );
                            return new Author('add.name <add.email@mail.com>');
                        },
                        $this
                    )
                )
            );

        $services['helper_set'] = function () use ($questionHelper) {
            return new HelperSet(['question' => $questionHelper]);
        };

        return $services;
    }

}
