<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\EventStore\Uuid;
use AppBundle\Message\Command;

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
     * @var int
     */
    private $version;

    /**
     * @param Uuid $id
     * @param string $firstName
     * @param string $lastName
     * @param int $version
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
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}
