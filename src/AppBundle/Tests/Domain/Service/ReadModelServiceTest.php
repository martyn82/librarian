<?php

namespace AppBundle\Tests\Domain\Service;

use AppBundle\Domain\DTO\Book;
use AppBundle\Domain\Event\BookRegistered;
use AppBundle\Domain\Service\ReadModelService;
use AppBundle\EventStore\Event;
use AppBundle\EventStore\Guid;
use AppBundle\Service\EventBus;
use AppBundle\Domain\Service\ObjectNotFoundException;

class ReadModelServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithBookRegisteredPropagatesToCorrectHandler()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $event = new BookRegistered($id, $title);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new ReadModelService($eventBus);
        $readModel->handle($event);

        $book = $readModel->getBook($id);
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

        $readModel = new ReadModelService($eventBus);
        $readModel->handleBookRegistered($event);

        $book = $readModel->getBook($id);
        self::assertEquals($title, $book->getTitle());
    }

    public function testGetBookWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = new ReadModelService($eventBus);
        $readModel->getBook(Guid::createNew());
    }
}
