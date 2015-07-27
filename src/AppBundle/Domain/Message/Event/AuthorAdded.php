<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class AuthorAdded extends Event
{
    /**
     * @Serializer\Type("AppBundle\EventSourcing\EventStore\Uuid")
     * @var Uuid
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
     * @param Uuid $id
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(Uuid $id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
}
