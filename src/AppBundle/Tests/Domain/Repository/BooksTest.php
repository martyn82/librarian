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
        $title = 'foo';
        $book = Book::register($id, $title);

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('save')
            ->with($id, $book->getUncommittedChanges());

        $repository = new Books($storage);
        $repository->store($book);
    }
}
