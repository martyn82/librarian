<?php

namespace AppBundle\Tests\EventStore;

use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\EventStore\AggregateNotFoundException;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;
use AppBundle\EventStore\Storage\Storage;
use AppBundle\Message\Event;
use AppBundle\Message\Events;
use AppBundle\MessageBus\EventBus;

class EventStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return EventBus
     */
    private function getEventBus()
    {
        return $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return Storage
     */
    private function getStorage()
    {
        return $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSaveEventsForAggregateCallsStorage()
    {
        $id = Guid::createNew();
        $events = new Events(
            [
                new FirstEvent(),
                new SecondEvent()
            ]
        );

        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();

        $storage->expects(self::exactly($events->getIterator()->count()))
            ->method('upsert')
            ->with($id);

        $store = new EventStore($eventBus, $storage);
        $store->save($id, $events);
    }

    public function testGetEventsForForUnknownAggregateThrowsException()
    {
        self::setExpectedException(AggregateNotFoundException::class);

        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();

        $store = new EventStore($eventBus, $storage);
        $store->getEventsForAggregate(Guid::createNew());
    }

    public function testGetEventsForAggregateCallsStorage()
    {
        $id = Guid::createNew();
        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();

        $storage->expects(self::any())
            ->method('contains')
            ->with($id->getValue())
            ->will(self::returnValue(true));

        $storage->expects(self::once())
            ->method('find')
            ->with($id->getValue())
            ->will(self::returnValue([]));

        $store = new EventStore($eventBus, $storage);

        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $store->save($id, new Events([$event]));
        $store->getEventsForAggregate($id);
    }

    public function testGetEventsForAggregateWillReturnEvents()
    {
        $id = Guid::createNew();
        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();

        $event = new BookAdded($id, 'foo');

        $eventStructure = [
            'timeStamp' => time(),
            'dateTime' => date('r'),
            'identity' => $id->getValue(),
            'payload' => serialize($event)
        ];

        $storage->expects(self::any())
            ->method('contains')
            ->with($id->getValue())
            ->will(self::returnValue(true));

        $storage->expects(self::once())
            ->method('find')
            ->with($id->getValue())
            ->will(self::returnValue([$eventStructure]));

        $store = new EventStore($eventBus, $storage);
        $events = $store->getEventsForAggregate($id);

        self::assertInstanceOf(Events::class, $events);
        self::assertCount(1, $events->getIterator());
    }

    public function testSaveEventsForAggregatePublishesEvents()
    {
        $id = Guid::createNew();
        $events = new Events(
            [
                new FirstEvent(),
                new SecondEvent()
            ]
        );

        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();

        $eventBus->expects(self::exactly($events->getIterator()->count()))
            ->method('publish')
            ->with(self::logicalOr($events->getIterator()->offsetGet(0), $events->getIterator()->offsetGet(1)));

        $store = new EventStore($eventBus, $storage);
        $store->save($id, $events);
    }
}

class FirstEvent extends Event {}
class SecondEvent extends Event {}
