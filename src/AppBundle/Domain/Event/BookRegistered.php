<?php

namespace AppBundle\Domain\Event;

use AppBundle\Domain\Descriptor\BookDescriptor;
use AppBundle\EventStore\Event;
use AppBundle\EventStore\Guid;

class BookRegistered extends Event
{
    use BookDescriptor;

    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     * @param string $title
     */
    public function __construct(Guid $id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @return Guid
     */
    public function getId()
    {
        return $this->id;
    }
}
