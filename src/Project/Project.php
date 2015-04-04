<?php
namespace Samurai\Project;

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
     * @var Authors
     */
    private $authors;

    /**
     * @var array
     */
    private $keywords;

    /**
     * @var string
     */
    private $homepage;

    /**
     * @var Packages
     */
    private $packages;

    /**
     *
     */
    public function __construct()
    {
        $this->setAuthors(new Authors());
        $this->setPackages(new Packages());
    }

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
     * Getter of $authors
     *
     * @return Authors
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Setter of $authors
     *
     * @param Authors $authors
     */
    public function setAuthors(Authors $authors)
    {
        $this->authors = $authors;
    }

    /**
     * @param Author $author
     */
    public function addAuthor(Author $author)
    {
        $this->authors[] = $author;
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
     * Getter of $package
     *
     * @return Packages
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Setter of $packages
     *
     * @param Packages $packages
     */
    public function setPackages(Packages $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @param Package $package
     */
    public function addPackage(Package $package)
    {
        $this->packages[] = $package;
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
            'authors' => $this->getAuthors()->toArray(),
            'autoload' => $this->getPackages()->toArray(),
        ];
    }
}
