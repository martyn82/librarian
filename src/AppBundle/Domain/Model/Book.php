<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\Message\Event\AuthorAdded;
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
     * @var array
     */
    private $authors = [];

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
     * @param string $firstName
     * @param string $lastName
     */
    public function addAuthor(Guid $id, $firstName, $lastName)
    {
        $this->applyChange(new AuthorAdded($id, $this->id, $firstName, $lastName));
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

    /**
     * @param AuthorAdded $event
     */
    protected function applyAuthorAdded(AuthorAdded $event)
    {
        $this->authors[] = $event->getId();
    }
}
