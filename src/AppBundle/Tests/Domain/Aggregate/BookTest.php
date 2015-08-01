<?php

namespace AppBundle\Tests\Domain\Aggregate;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\Domain\Aggregate\BookUnavailableException;
use AppBundle\Domain\Message\Event\BookCheckedOut;
use AppBundle\Domain\Message\Event\BookReturned;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\EventSourcing\EventStore\Uuid;

class BookTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckOutCanOnlyBeDoneOnce()
    {
        $id = Uuid::createNew();
        $book = new Book($id);

        $book->checkOut();

        self::setExpectedException(BookUnavailableException::class);
        $book->checkOut();
    }

    public function testCheckInMakesBookAvailable()
    {
        $id = UUid::createNew();
        $book = new Book($id);

        $book->checkOut();
        $book->checkIn();

        $events = $book->getUncommittedChanges()
            ->getIterator()
            ->getArrayCopy();

        self::assertInstanceOf(BookCheckedOut::class, $events[0]);
        self::assertInstanceOf(BookReturned::class, $events[1]);
    }

    public function testCheckInIsIdempotent()
    {
        $id = Uuid::createNew();
        $book = new Book($id);

        $book->checkIn();

        $events = $book->getUncommittedChanges()
            ->getIterator()
            ->getArrayCopy();

        self::assertCount(0, $events);
    }
}
