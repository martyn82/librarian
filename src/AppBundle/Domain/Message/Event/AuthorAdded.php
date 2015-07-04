<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\Domain\ModelDescriptor\AuthorDescriptor;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

final class AuthorAdded extends Event
{
    use AuthorDescriptor;

    /**
     * @var Guid
     */
    private $id;

    /**
     * @var Guid
     */
    private $bookId;

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
}
