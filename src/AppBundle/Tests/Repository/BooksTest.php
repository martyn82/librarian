<?php

namespace AppBundle\Tests\Repository;

use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;
use AppBundle\Model\Book;
use AppBundle\Repository\Books;

class BooksTest extends \PHPUnit_Framework_TestCase
{
    public function testStoreBookCallsSaveOnStorage()
    {
        $id = Guid::createNew();
        $book = Book::register($id);

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('save')
            ->with($id);

        $repository = new Books($storage);
        $repository->store($book);
    }
}