<?php
namespace Samurai\Project\Question;

use ICanBoogie\Inflector;
use Samurai\Project\Package;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question as SimpleQuestion;

/**
 * Class PackageQuestion
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class PackageQuestion extends Question
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if($this->confirmFirstPackage($input, $output)){
            $this->setPackage($input, $output, true);
        }
        while ($this->confirmAdditionalPackages($input, $output)) {
            $this->setPackage($input, $output, false);
        }
        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    private function confirmFirstPackage(InputInterface $input, OutputInterface $output)
    {
        return $this->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>Do you want to add a namespace?[y]</question>')
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    private function confirmAdditionalPackages(InputInterface $input, OutputInterface $output)
    {
        return $this->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>Do you want to add another namespace?[n]</question>', false)
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param bool $isFirstNamespace
     */
    private function setPackage(InputInterface $input, OutputInterface $output, $isFirstNamespace)
    {
        $package = new Package();
        if($isFirstNamespace){
            $package->setNamespace($this->askForFirstNamespace($input, $output));
        }else{
            $package->setNamespace($this->askForAdditionalNamespace($input, $output));
        }
        $package->setPathList(explode(',', $this->askForPathList($input, $output)));
        $this->getProject()->addPackage($package);
    }

    /**
     * @param $input
     * @param $output
     * @return mixed
     */
    private function askForFirstNamespace($input, $output)
    {
        return $this->ask(
            $input,
            $output,
            $this->buildFirstNamespaceQuestion()
        );
    }

    /**
     * @param $input
     * @param $output
     * @return mixed
     */
    private function askForAdditionalNamespace($input, $output)
    {
        return $this->ask(
            $input,
            $output,
            $this->buildAdditionalNamespaceQuestion()
        );
    }

    /**
     * @return SimpleQuestion
     */
    private function buildFirstNamespaceQuestion()
    {
        $defaultNamespace = $this->retrieveDefaultNamespace();
        return new SimpleQuestion(
            '<question>Enter the namespace:['.$defaultNamespace.']</question>',
            $defaultNamespace
        );
    }

    /**
     * @return SimpleQuestion
     */
    private function buildAdditionalNamespaceQuestion()
    {
        return new SimpleQuestion('<question>Enter the namespace:</question>');
    }

    /**
     * @param $input
     * @param $output
     * @return mixed
     */
    private function askForPathList($input, $output)
    {
        return $this->ask(
            $input,
            $output,
            $this->buildPathListQuestion()
        );
    }

    /**
     * @return SimpleQuestion
     */
    private function buildPathListQuestion()
    {
        return new SimpleQuestion(
            '<question>Enter the path list (path separated by a comma)[src/,tests/]:</question>',
            'src/,tests/'
        );
    }

    /**
     * @return string
     */
    private function retrieveDefaultNamespace()
    {
        $inflector = Inflector::get();
        return $inflector->camelize(explode('/', $this->getProject()->getName())[1]);
    }
}
