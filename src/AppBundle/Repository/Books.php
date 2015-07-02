<?php

namespace AppBundle\Repository;

use AppBundle\Model\Book;
use AppBundle\EventStore\EventStore;

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
    public function add(Book $book)
    {
        $this->storage->save($book->getId(), $book->getUncommittedChanges());
    }
}
