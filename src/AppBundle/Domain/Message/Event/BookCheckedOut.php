<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class BookCheckedOut extends Event
{
    /**
     * @Serializer\Type("AppBundle\EventSourcing\EventStore\Uuid")
     * @var Uuid
     */
    private $id;

    /**
     * @Serializer\Type("AppBundle\EventSourcing\EventStore\Uuid")
     * @var Uuid
     */
    private $userId;

    /**
     * @param Uuid $id
     * @param Uuid $userId
     */
    public function __construct(Uuid $id, Uuid $userId)
    {
        $this->id = $id;
        $this->userId = $userId;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Uuid
     */
    public function getUserId()
    {
        return $this->userId;
    }
}