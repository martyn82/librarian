<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\EventStore\AggregateRoot;
use AppBundle\EventStore\Guid;
use AppBundle\Domain\ModelDescriptor\BookDescriptor;

class Book extends AggregateRoot
{
    use BookDescriptor;

    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     * @param string $title
     * @return Book
     */
    public static function add(Guid $id, $title)
    {
        $instance = new self($id);
        $instance->applyChange(new BookAdded($instance->getId(), $title));
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
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @param BookAdded $event
     */
    protected function applyBookAdded(BookAdded $event)
    {
        $this->id = $event->getId();
        $this->title = $event->getTitle();
    }

    /**
     * @param AuthorAdded $event
     */
    protected function applyAuthorAdded(AuthorAdded $event)
    {
        $this->authors[] = $event->getId();
    }
}
