<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class AuthorAdded extends Event
{
    /**
     * @Serializer\Type("AppBundle\EventStore\Guid")
     * @var Guid
     */
    private $id;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $firstName;

    /**
     * @Serializer\Type("string")
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
