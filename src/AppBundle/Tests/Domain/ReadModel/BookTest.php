<?php

namespace AppBundle\Tests\Domain\ReadModel;

use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\EventSourcing\EventStore\Uuid;

class BookTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $id = Uuid::createNew();
        $book = new Book($id, new Authors(), 'title', 'isbn', false, 1);
        $serialized = $book->serialize();
        $deserialized = Book::deserialize($serialized);

        self::assertEquals($book, $deserialized);
    }
}