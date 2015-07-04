<?php

namespace AppBundle\Domain\Repository;

use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\EventStore;

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
     * @param Book $book
     */
    public function store(Book $book)
    {
        $this->storage->save($book->getId(), $book->getUncommittedChanges());
    }
}
