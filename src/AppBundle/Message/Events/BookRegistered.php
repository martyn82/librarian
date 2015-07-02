<?php

namespace AppBundle\Message\Events;

use AppBundle\Message\Event;
use AppBundle\Model\Guid;

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
