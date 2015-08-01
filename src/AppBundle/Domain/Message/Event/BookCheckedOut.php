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
     * @param Uuid $id
     */
    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }
}