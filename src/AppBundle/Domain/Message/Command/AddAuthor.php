<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\EventStore\Guid;
use AppBundle\Message\Command;

final class AddAuthor implements Command
{
    /**
     * @var Guid
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
     * @param Guid $id
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(Guid $id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return Guid
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
}
