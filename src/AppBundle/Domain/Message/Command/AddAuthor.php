<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Command;

final class AddAuthor implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var integer
     */
    private $version;

    /**
     * @param Uuid $id
     * @param string $firstName
     * @param string $lastName
     * @param integer $version
     */
    public function __construct(Uuid $id, $firstName, $lastName, $version)
    {
        $this->id = $id;
        $this->firstName = (string) $firstName;
        $this->lastName = (string) $lastName;
        $this->version = (int) $version;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }
}
