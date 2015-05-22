<?php
namespace Samurai\Module\Planner;

use Samurai\Task\ITask;
use Samurai\Task\Planner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @param IPlannerBuilder $plannerBuilder
     */
    public function __construct(IPlannerBuilder $plannerBuilder)
    {
        $this->plannerBuilder = $plannerBuilder;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
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
}
