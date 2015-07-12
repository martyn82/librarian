<?php

namespace AppBundle\Domain\Repository;

use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\AggregateRoot;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Uuid;
use AppBundle\EventStore\Repository;

class Books implements Repository
{
    /**
     * @var EventStore
     */
    private $storage;

    /**
     * @param EventStore $storage
     */
    public function __construct(EventStore $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Uuid $id
     * @return AggregateRoot
     */
    public function findById(Uuid $id)
    {
        $events = $this->storage->getEventsForAggregate($id);

        $book = new Book($id);
        $book->loadFromHistory($events);

        return $book;
    }

    /**
     * @see \AppBundle\EventStore\Repository::store()
     */
    public function store(AggregateRoot $aggregate, $expectedPlayhead = EventStore::FIRST_VERSION)
    {
        $this->storage->save($aggregate->getId(), $aggregate->getUncommittedChanges(), $expectedPlayhead);
    }
}
