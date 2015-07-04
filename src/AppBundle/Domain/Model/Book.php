<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\Message\Event\BookRegistered;
use AppBundle\EventStore\AggregateRoot;
use AppBundle\EventStore\Guid;

class Book extends AggregateRoot
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     * @param string $title
     * @return Book
     */
    public static function register(Guid $id, $title)
    {
        $instance = new self($id);
        $instance->applyChange(new BookRegistered($instance->getId(), $title));
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
    }
}
