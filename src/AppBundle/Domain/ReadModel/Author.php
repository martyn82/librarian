<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Domain\Storage\Document;
use AppBundle\EventStore\Guid;

class Author extends Document
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
    final public function getId()
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
