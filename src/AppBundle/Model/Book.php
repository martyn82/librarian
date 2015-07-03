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
     * @return Book
     */
    public static function register(Guid $id)
    {
        $instance = new self($id);
        $instance->applyChange(new BookRegistered($instance->getId()));
        return $instance;
    }

    /**
     * @param Guid $id
     */
    public function __construct(Guid $id)
    {
        $this->id = $id;
        parent::__construct();
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
