<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

final class AuthorAdded extends Event
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @var Guid
     */
    private $bookId;

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
     * @param Guid $bookId
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(Guid $id, Guid $bookId, $firstName, $lastName)
    {
        $this->id = $id;
        $this->bookId = $bookId;
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
     * @return Guid
     */
    public function getBookId()
    {
        return $this->bookId;
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
