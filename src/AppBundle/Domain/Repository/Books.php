<?php

namespace AppBundle\Domain\Repository;

use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;

class Books
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
     * @param Guid $id
     * @return Book
     */
    public function findById(Guid $id)
    {
        $events = $this->storage->getEventsForAggregate($id);

        $book = new Book($id);
        $book->loadFromHistory($events);

        return $book;
    }

    /**
     * @param Book $book
     */
    public function store(Book $book)
    {
        $this->storage->save($book->getId(), $book->getUncommittedChanges());
    }
}
