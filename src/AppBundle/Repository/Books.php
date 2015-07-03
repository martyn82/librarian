<?php

namespace AppBundle\Repository;

use AppBundle\EventStore\EventStore;
use AppBundle\Model\Book;

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
     * @param Book $book
     */
    public function store(Book $book)
    {
        $this->storage->save($book->getId(), $book->getUncommittedChanges());
    }
}
