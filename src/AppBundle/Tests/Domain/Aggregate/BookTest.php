<?php

namespace AppBundle\Tests\Domain\Aggregate;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\Domain\Aggregate\BookUnavailableException;
use AppBundle\EventSourcing\EventStore\Uuid;

class BookTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckOutCanOnlyBeDoneOnce()
    {
        $id = Uuid::createNew();
        $book = new Book($id);

        $book->checkout();

        self::setExpectedException(BookUnavailableException::class);
        $book->checkout();
    }
}
