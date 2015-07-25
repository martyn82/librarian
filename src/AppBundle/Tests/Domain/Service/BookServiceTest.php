<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Storage\MemoryStorage;
use AppBundle\Domain\Storage\Storage;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Uuid;
use AppBundle\Message\Event;

class BookServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithBookAddedPropagatesToCorrectHandler()
    {
        $id = Uuid::createNew();
        $title = 'foo';
        $isbn = 'bar';

        $event = new BookAdded($id, [], $title, $isbn);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('upsert')
            ->with($id, self::anything());

        $service = new BookService($storage);
        $service->on($event);
    }

    public function testHandlerForEventBookAdded()
    {
        $id = Uuid::createNew();
        $title = 'foo';
        $isbn = 'bar';

        $event = new BookAdded($id, [], $title, $isbn);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('upsert')
            ->with($id, self::anything());

        $service = new BookService($storage);
        $service->onBookAdded($event);
    }

    public function testHandleWithAuthorAddedPropagatesToCorrectHandler()
    {
        $bookId = Uuid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('upsert')
            ->with($bookId, self::anything());

        $storage->expects(self::once())
            ->method('find')
            ->with($bookId)
            ->will(self::returnValue(new Book($bookId, new Authors(), 'foo', 'bar', EventStore::FIRST_VERSION)));

        $service = new BookService($storage);
        $service->on(new AuthorAdded($bookId, $firstName, $lastName));
    }

    public function testHandlerForEventAuthorAdded()
    {
        $bookId = Uuid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('upsert')
            ->with($bookId, self::anything());

        $storage->expects(self::once())
            ->method('find')
            ->with($bookId)
            ->will(self::returnValue(new Book($bookId, new Authors(), 'foo', 'bar', EventStore::FIRST_VERSION)));

        $service = new BookService($storage);
        $service->onAuthorAdded(new AuthorAdded($bookId, $firstName, $lastName));
    }

    public function testGetBookWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($storage);
        $service->getBook(Uuid::createNew());
    }

    public function testHandleWithUnsupportedEventThrowsException()
    {
        self::setExpectedException(\InvalidArgumentException::class);

        $event = $this->getMockBuilder(Event::class)
           ->getMock();

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($storage);
        $service->on($event);
    }

    public function testBookIsAvailableAfterBookAdded()
    {
        $bookId = Uuid::createNew();
        $title = 'foo';
        $isbn = 'bar';
        $firstName = 'bar';
        $lastName = 'baz';
        $authors = [
            new AuthorAdded($bookId, $firstName, $lastName)
        ];

        $storage = new MemoryStorage();
        $service = new BookService($storage);
        $service->on(new BookAdded($bookId, $authors, $title, $isbn));

        $book = $service->getBook($bookId);

        self::assertEquals($bookId, $book->getId());
        self::assertEquals($title, $book->getTitle());

        foreach ($book->getAuthors()->getIterator() as $author) {
            /* @var $author Author */
            self::assertEquals($firstName, $author->getFirstName());
            self::assertEquals($lastName, $author->getLastName());
        }
    }

    public function testGetAllRetrievesAll()
    {
        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('findAll');

        $service = new BookService($storage);
        $service->getAll();
    }
}
