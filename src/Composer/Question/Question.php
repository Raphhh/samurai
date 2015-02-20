<?php
namespace Samurai\Composer\Question;

use Samurai\Composer\Project;

/**
 * Class Question
 * @package Samurai\Composer\Question
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
