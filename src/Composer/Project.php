<?php
namespace Samurai\Composer;

/**
 * Class Project
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Project 
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $bootstrapName;

    /**
     * @var string
     */
    private $bootstrapVersion;

    /**
     * @var string
     */
    private $directoryPath;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $authorName;

    /**
     * @var string
     */
    private $authorEmail;

    /**
     * @var array
     */
    private $keywords;

    /**
     * @var string
     */
    private $homepage;

    /**
     * Getter of $name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter of $name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * Getter of $bootstrapName
     *
     * @return string
     */
    public function getBootstrapName()
    {
        return $this->bootstrapName;
    }

    /**
     * Setter of $bootstrapName
     *
     * @param string $bootstrapName
     */
    public function setBootstrapName($bootstrapName)
    {
        $this->bootstrapName = (string)$bootstrapName;
    }

    /**
     * Getter of $bootstrapVersion
     *
     * @return string
     */
    public function getBootstrapVersion()
    {
        return $this->bootstrapVersion;
    }

    /**
     * Setter of $bootstrapVersion
     *
     * @param string $bootstrapVersion
     */
    public function setBootstrapVersion($bootstrapVersion)
    {
        $this->bootstrapVersion = (string)$bootstrapVersion;
    }

    /**
     * Getter of $directoryPath
     *
     * @return string
     */
    public function getDirectoryPath()
    {
        return $this->directoryPath;
    }

    /**
     * Setter of $directoryPath
     *
     * @param string $directoryPath
     */
    public function setDirectoryPath($directoryPath)
    {
        $this->directoryPath = (string)$directoryPath;
    }

    /**
     * @return mixed
     */
    public function getPackage()
    {
        return explode('/', $this->getName())[1];
    }

    /**
     * Getter of $description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Setter of $description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
    }

    /**
     * Getter of $authorName
     *
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * Setter of $authorName
     *
     * @param string $authorName
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = (string)$authorName;
    }

    /**
     * Getter of $authorEmail
     *
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * Setter of $authorEmail
     *
     * @param string $authorEmail
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = (string)$authorEmail;
    }

    /**
     * Getter of $keywords
     *
     * @return array
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Setter of $keywords
     *
     * @param array $keywords
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Getter of $homepage
     *
     * @return string
     */
    public function getHomepage()
    {
        return $this->homepage;
    }

    /**
     * Setter of $homepage
     *
     * @param string $homepage
     */
    public function setHomepage($homepage)
    {
        $this->homepage = (string)$homepage;
    }

    /**
     * @return array
     */
    public function toConfig()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'keywords' => $this->getKeywords(),
            'homepage' => $this->getHomepage(),
            'authors' => [
                [
                    'name' => $this->getAuthorName(),
                    'email' => $this->getAuthorEmail(),
                ]
            ],
        ];
    }
}
