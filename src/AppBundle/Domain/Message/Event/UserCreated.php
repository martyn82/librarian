<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class UserCreated extends Event
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
    private $userName;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $emailAddress;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $fullName;

    /**
     * @param Uuid $id
     * @param string $userName
     * @param string $emailAddress
     * @param string $fullName
     */
    public function __construct(Uuid $id, $userName, $emailAddress, $fullName)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->emailAddress = $emailAddress;
        $this->fullName = $fullName;
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
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }
}