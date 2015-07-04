<?php

namespace AppBundle\Tests\Domain\Service;

use AppBundle\Domain\Message\Event\BookRegistered;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\BookReadModel;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;
use AppBundle\MessageBus\EventBus;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Model\AuthorView;

class BookReadModelTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithBookRegisteredPropagatesToCorrectHandler()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookRegistered($id, $title);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new BookReadModel($eventBus);
        $readModel->handle($event);

        $book = $readModel->getBook($id);
        self::assertInstanceOf(BookView::class, $book);
        self::assertEquals($title, $book->getTitle());
    }

    public function testHandlerForEventBookRegistered()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookRegistered($id, $title);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new BookReadModel($eventBus);
        $readModel->handleBookRegistered($event);

        $book = $readModel->getBook($id);
        self::assertInstanceOf(BookView::class, $book);
        self::assertEquals($title, $book->getTitle());
    }

    public function testHandleWithAuthorAddedPropagatesToCorrectHandler()
    {
        $bookId = Guid::createNew();
        $id = Guid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new BookReadModel($eventBus);

        // Given, a book with $bookId is available
        $readModel->handleBookRegistered(new BookRegistered($bookId, 'foo'));

        // When AuthorAdded for book with $bookId
        $event = new AuthorAdded($id, $bookId, $firstName, $lastName);
        $readModel->handle($event);

        // Then book with $bookId contains new Author
        $book = $readModel->getBook($bookId);

        self::assertCount(1, $book->getAuthors());
        self::assertInstanceOf(AuthorView::class, $book->getAuthors()[0]);
        self::assertEquals($firstName, $book->getAuthors()[0]->getFirstName());
        self::assertEquals($lastName, $book->getAuthors()[0]->getLastName());
    }

    public function testHandlerForEventAuthorAdded()
    {
        $bookId = Guid::createNew();
        $id = Guid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new BookReadModel($eventBus);

        // Given, a book with $bookId is available
        $readModel->handleBookRegistered(new BookRegistered($bookId, 'foo'));

        // When AuthorAdded for book with $bookId
        $event = new AuthorAdded($id, $bookId, $firstName, $lastName);
        $readModel->handleAuthorAdded($event);

        // Then book with $bookId contains Author
        $book = $readModel->getBook($bookId);

        self::assertCount(1, $book->getAuthors());
        self::assertInstanceOf(AuthorView::class, $book->getAuthors()[0]);
        self::assertEquals($firstName, $book->getAuthors()[0]->getFirstName());
        self::assertEquals($lastName, $book->getAuthors()[0]->getLastName());
    }

    public function testGetBookWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new BookReadModel($eventBus);
        $readModel->getBook(Guid::createNew());
    }

    public function testHandleWithUnsupportedEventThrowsException()
    {
        self::setExpectedException(\InvalidArgumentException::class);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $readModel = new BookReadModel($eventBus);
        $readModel->handle($event);
    }
}
