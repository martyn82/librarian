<?php

namespace AppBundle\Tests\Domain\Repository;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Events;

class BooksTest extends \PHPUnit_Framework_TestCase
{
    public function testStoreBookCallsSaveOnStorage()
    {
        $id = Guid::createNew();
        $title = 'foo';
        $book = Book::add($id, $title);

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('save')
            ->with($id, $book->getUncommittedChanges(), self::anything());

        $repository = new Books($storage);
        $repository->store($book, -1);
    }

    public function testFindBookByIdLoadsBookFromHistory()
    {
        $bookId = Guid::createNew();

        $title = 'foo';
        $authorFirstName = 'first';
        $authorLastName = 'last';

        $expectedBook = Book::add($bookId, $title);
        $expectedBook->addAuthor($authorFirstName, $authorLastName);

        $events = new Events(
            [
                new BookAdded($bookId, $title),
                new AuthorAdded($bookId, $authorFirstName, $authorLastName)
            ]
        );

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('getEventsForAggregate')
            ->with($bookId)
            ->will(self::returnValue($events));

        $repository = new Books($storage);
        $actualBook = $repository->findById($bookId);

        self::assertEquals($expectedBook->getId(), $actualBook->getId());
    }
}
