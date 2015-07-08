<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Command;
use AppBundle\Message\Event;
use AppBundle\MessageBus\CommandBus;

class BookServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithBookAddedPropagatesToCorrectHandler()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookAdded($id, $title);

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($commandBus);
        $service->handle($event);

        $book = $service->getBook($id);

        self::assertInstanceOf(Book::class, $book);
        self::assertEquals($id, $book->getId());
        self::assertEquals($title, $book->getTitle());
    }

    public function testHandlerForEventBookAdded()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookAdded($id, $title);

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($commandBus);
        $service->handleBookAdded($event);

        $book = $service->getBook($id);

        self::assertInstanceOf(Book::class, $book);
        self::assertEquals($id, $book->getId());
        self::assertEquals($title, $book->getTitle());
    }

    public function testHandleWithAuthorAddedPropagatesToCorrectHandler()
    {
        $bookId = Guid::createNew();
        $id = Guid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($commandBus);

        // Given, a book with $bookId is available
        $service->handleBookAdded(new BookAdded($bookId, 'foo'));

        // When AuthorAdded for book with $bookId
        $service->handle(new AuthorAdded($id, $bookId, $firstName, $lastName));

        // Then book with $bookId contains new Author
        $book = $service->getBook($bookId);

        self::assertCount(1, $book->getAuthors());

        $authors = iterator_to_array($book->getAuthors()->getIterator());

        self::assertInstanceOf(Author::class, $authors[0]);
        self::assertEquals($id, $authors[0]->getId());
        self::assertEquals($firstName, $authors[0]->getFirstName());
        self::assertEquals($lastName, $authors[0]->getLastName());
    }

    public function testHandlerForEventAuthorAdded()
    {
        $bookId = Guid::createNew();
        $id = Guid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($commandBus);

        // Given, a book with $bookId is available
        $service->handleBookAdded(new BookAdded($bookId, 'foo'));

        // When AuthorAdded for book with $bookId
        $service->handleAuthorAdded(new AuthorAdded($id, $bookId, $firstName, $lastName));

        // Then book with $bookId contains Author
        $book = $service->getBook($bookId);

        self::assertCount(1, $book->getAuthors());

        $authors = iterator_to_array($book->getAuthors()->getIterator());

        self::assertInstanceOf(Author::class, $authors[0]);
        self::assertEquals($id, $authors[0]->getId());
        self::assertEquals($firstName, $authors[0]->getFirstName());
        self::assertEquals($lastName, $authors[0]->getLastName());
    }

    public function testGetBookWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($commandBus);
        $service->getBook(Guid::createNew());
    }

    public function testHandleWithUnsupportedEventThrowsException()
    {
        self::setExpectedException(\InvalidArgumentException::class);

        $event = $this->getMockBuilder(Event::class)
           ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new BookService($commandBus);
        $service->handle($event);
    }
}
