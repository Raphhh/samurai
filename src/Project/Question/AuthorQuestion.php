<?php
namespace Samurai\Project\Question;

use Samurai\Project\Author;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class AuthorQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class AuthorQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInitialAuthor($input, $output);
        $this->setAdditionalAuthors($input, $output);
        return $this->getProject()->getAuthors()->count() ? ITask::NO_ERROR_CODE : ITask::BLOCKING_ERROR_CODE;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function setInitialAuthor(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->setAuthorFromGit($input, $output);
        } catch (\Exception $e) {
            $this->setAuthorFromQuestion($input, $output);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function setAuthorFromGit(InputInterface $input, OutputInterface $output)
    {
        $author = new Author($this->retrieveGitUser());
        if ($this->confirmAuthor($input, $output, $author)) {
            $this->getProject()->addAuthor($author);
        } else {
            $this->setAuthorFromQuestion($input, $output);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function setAuthorFromQuestion(InputInterface $input, OutputInterface $output)
    {
        $this->getProject()->addAuthor(
            $this->ask(
                $input,
                $output,
                $this->buildAuthorQuestion()
            )
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function setAdditionalAuthors(InputInterface $input, OutputInterface $output)
    {
        while ($this->confirmAdditionalAuthor($input, $output)) {
            $this->setAuthorFromQuestion($input, $output);
        }
    }

    /**
     * @return SimpleQuestion
     */
    private function buildAuthorQuestion()
    {
        $question = new SimpleQuestion('<question>Enter the author (name <mail@mail.com>):</question>');
        $question->setValidator($this->buildValidator());
        $question->setMaxAttempts(3);

        return $question;
    }

    /**
     * @return callable
     */
    private function buildValidator()
    {
        return function ($answer) {
            return new Author($answer);
        };
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    private function confirmAdditionalAuthor(InputInterface $input, OutputInterface $output)
    {
        return $this->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>Do you want to add another author?[n]</question>', false)
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Author $author
     * @return mixed
     */
    private function confirmAuthor(InputInterface $input, OutputInterface $output, Author $author)
    {
        return $this->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>Do you confirm this author "'.$author.'"?[y]</question>')
        );
    }

    /**
     * @return string
     */
    private function retrieveGitUser()
    {
        $config = $this->getService('git')->config();
        if(isset($config['user.name']) && isset($config['user.email'])){
            return $config['user.name'] . ' <' . $config['user.email'] . '>';
        }
        return '';
    }
}
