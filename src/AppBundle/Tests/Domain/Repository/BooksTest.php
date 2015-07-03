<?php

namespace AppBundle\Tests\Domain\Repository;

use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;

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
