<?php

namespace AppBundle\Tests\Domain\Service;

use AppBundle\Domain\Message\Event\BookRegistered;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\BookReadModel;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;
use AppBundle\MessageBus\EventBus;

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

    public function testGetBookWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new BookReadModel($eventBus);
        $readModel->getBook(Guid::createNew());
    }
}
