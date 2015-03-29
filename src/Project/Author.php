<?php
namespace Samurai\Project;

/**
 * Class Author
 * @package Samurai\Project
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Author 
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * $author must be formatted like an email of the RFC 822
     * ex: name <mail@mail.com>
     * @param string $author
     */
    public function __construct($author)
    {
        if (preg_match('/^(?P<name>[- \.,\p{L}\'’]+) <(?P<email>.+?)>$/u', $author, $match)) {
            $this->setName($match['name']);
            $this->setEmail($match['email']);
        }else{
            throw new \InvalidArgumentException(sprintf(
                'Invalid author "%s".  Must be in the format: "name <mail@mail.com>"',
                $author
            ));
        }
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
     * Getter of $email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Setter of $email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new \InvalidArgumentException(sprintf('Invalid email "%s"', $email));
        }
        $this->email = (string)$email;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return[
            'name' => $this->getName(),
            'email' => $this->getEmail(),
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() . ' <' . $this->getEmail() . '>';
    }
}
