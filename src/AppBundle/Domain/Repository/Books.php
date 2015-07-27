<?php

namespace AppBundle\Domain\Repository;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\EventSourcing\EventStore\AggregateRoot;
use AppBundle\EventSourcing\EventStore\ConcurrencyException;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\EventStore\Uuid;

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
     * @param AggregateRoot $aggregate
     * @param integer $expectedPlayHead
     * @throws ConcurrencyException
     */
    public function store(AggregateRoot $aggregate, $expectedPlayHead = -1)
    {
        $this->storage->save($aggregate->getId(), $aggregate->getUncommittedChanges(), $expectedPlayHead);
    }
}
