<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

final class BookAdded extends Event
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @var string
     */
    private $title;

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

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
