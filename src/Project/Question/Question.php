<?php
namespace Samurai\Project\Question;

use Samurai\Project\Project;

/**
 * Class Question
 * @package Samurai\Project\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
abstract class Question extends \Samurai\Question\Question
{

    /**
     * Getter of $project
     *
     * @return Project
     */
    protected function getProject()
    {
        return $this->getService('composer')->getProject();
    }
}
