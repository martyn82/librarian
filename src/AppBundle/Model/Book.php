<?php

namespace AppBundle\Model;

use AppBundle\EventStore\AggregateRoot;
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
    public function __construct(Guid $id)
    {
        parent::__construct();
        $this->applyChange(new BookRegistered($id));
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
