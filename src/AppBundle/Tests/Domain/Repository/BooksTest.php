<?php

namespace AppBundle\Tests\Domain\Repository;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Events;

class BooksTest extends \PHPUnit_Framework_TestCase
{
    public function testStoreBookCallsSaveOnStorage()
    {
        $id = Uuid::createNew();
        $title = 'foo';
        $isbn = 'isbn';
        $authors = [];
        $book = Book::add($id, $authors, $title, $isbn);

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('save')
            ->with($id, $book->getUncommittedChanges(), self::anything());

        $repository = new Books($storage);
        $repository->store($book);
    }

    public function testFindBookByIdLoadsBookFromHistory()
    {
        $bookId = Uuid::createNew();

        $title = 'foo';
        $isbn = 'isbn';
        $authors = [];
        $authorFirstName = 'first';
        $authorLastName = 'last';

        $expectedBook = Book::add($bookId, $authors, $title, $isbn);
        $expectedBook->addAuthor($authorFirstName, $authorLastName);

        $events = new Events(
            [
                new BookAdded($bookId, $authors, $title, $isbn),
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
