<?php
namespace Samurai\Module\Planner;

use Samurai\Task\ITask;
use Samurai\Task\Planner;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class PlannerAdapter
 * @package Samurai\Module\Planner
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class PlannerAdapter implements ITask
{
    /**
     * @var Planner
     */
    private $planner;

    /**
     * @var IPlannerBuilder
     */
    private $plannerBuilder;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @param IPlannerBuilder $plannerBuilder
     * @param QuestionHelper $questionHelper
     */
    public function __construct(IPlannerBuilder $plannerBuilder, QuestionHelper $questionHelper)
    {
        $this->plannerBuilder = $plannerBuilder;
        $this->questionHelper = $questionHelper;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if(!$this->questionHelper->ask($input, $output, $this->buildQuestion())){
            return ITask::NO_ERROR_CODE;
        }
        return $this->getPlanner()->execute($input, $output);
    }

    /**
     * Getter of $planner
     *
     * @return Planner
     */
    private function getPlanner()
    {
        if($this->planner == null){
            $this->setPlanner($this->plannerBuilder->create());
        }
        return $this->planner;
    }

    /**
     * Setter of $planner
     *
     * @param Planner $planner
     */
    private function setPlanner(Planner $planner)
    {
        $this->planner = $planner;
    }

    /**
     * @return ConfirmationQuestion
     */
    private function buildQuestion()
    {
        if($this->plannerBuilder->getName()){
            return new ConfirmationQuestion('<question>Do you want to execute the module "' . $this->plannerBuilder->getName() . '"?[y]</question>');
        }
        return new ConfirmationQuestion('<question>Do you want to execute the modules?[y]</question>');
    }
}
