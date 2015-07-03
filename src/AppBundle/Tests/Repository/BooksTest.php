<?php

namespace AppBundle\Tests\Repository;

use AppBundle\EventStore\EventStore;
use AppBundle\Model\Book;
use AppBundle\Repository\Books;
use AppBundle\Model\Guid;

class BooksTest extends \PHPUnit_Framework_TestCase
{
    public function testStoreBookCallsSaveOnStorage()
    {
        $id = Guid::createNew();
        $book = new Book($id);

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
