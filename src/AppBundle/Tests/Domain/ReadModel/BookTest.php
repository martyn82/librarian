<?php

namespace AppBundle\Tests\Domain\Service;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\Model\AuthorView;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\ReadModel\ObjectNotFoundException;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;
use AppBundle\MessageBus\EventBus;

class BookTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithBookAddedPropagatesToCorrectHandler()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookAdded($id, $title);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new Book($eventBus);
        $readModel->handle($event);

        $book = $readModel->getBook($id);

        self::assertInstanceOf(BookView::class, $book);
        self::assertEquals($id, $book->getId());
        self::assertEquals($title, $book->getTitle());
    }

    public function testHandlerForEventBookAdded()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookAdded($id, $title);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new Book($eventBus);
        $readModel->handleBookAdded($event);

        $book = $readModel->getBook($id);

        self::assertInstanceOf(BookView::class, $book);
        self::assertEquals($id, $book->getId());
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

        $readModel = new Book($eventBus);

        // Given, a book with $bookId is available
        $readModel->handleBookAdded(new BookAdded($bookId, 'foo'));

        // When AuthorAdded for book with $bookId
        $event = new AuthorAdded($id, $bookId, $firstName, $lastName);
        $readModel->handle($event);

        // Then book with $bookId contains new Author
        $book = $readModel->getBook($bookId);

        self::assertCount(1, $book->getAuthors());

        $authors = iterator_to_array($book->getAuthors()->getIterator());

        self::assertInstanceOf(AuthorView::class, $authors[0]);
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

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new Book($eventBus);

        // Given, a book with $bookId is available
        $readModel->handleBookAdded(new BookAdded($bookId, 'foo'));

        // When AuthorAdded for book with $bookId
        $event = new AuthorAdded($id, $bookId, $firstName, $lastName);
        $readModel->handleAuthorAdded($event);

        // Then book with $bookId contains Author
        $book = $readModel->getBook($bookId);

        self::assertCount(1, $book->getAuthors());

        $authors = iterator_to_array($book->getAuthors()->getIterator());

        self::assertInstanceOf(AuthorView::class, $authors[0]);
        self::assertEquals($id, $authors[0]->getId());
        self::assertEquals($firstName, $authors[0]->getFirstName());
        self::assertEquals($lastName, $authors[0]->getLastName());
    }

    public function testGetBookWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new Book($eventBus);
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

        $readModel = new Book($eventBus);
        $readModel->handle($event);
    }
}
