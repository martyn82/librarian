<?php

namespace AppBundle\Domain\Event;

use AppBundle\EventStore\Event;
use AppBundle\EventStore\Guid;

class BookRegistered extends Event
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     */
    public function __construct(Guid $id)
    {
        $this->id = $id;
    }

    /**
     * @return Guid
     */
    public function getId()
    {
        return $this->id;
    }
}
