<?php

namespace AppBundle\Model;

use AppBundle\EventStore\AggregateRoot;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Events\BookRegistered;

class Book extends AggregateRoot
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @var bool
     */
    private $registered;

    /**
     * @param Guid $id
     */
    public static function register(Guid $id)
    {
        $instance = new self();
        $instance->applyChange(new BookRegistered($id));
        return $instance;
    }

    /**
     * @see \AppBundle\EventStore\AggregateRoot::getId()
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param BookRegistered $event
     */
    protected function applyBookRegistered(BookRegistered $event)
    {
        $this->id = $event->getId();
        $this->registered = true;
    }
}
