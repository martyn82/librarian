<?php

namespace AppBundle\Tests\EventStore;

use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\EventStore\AggregateNotFoundException;
use AppBundle\EventStore\EventClassMap;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;
use AppBundle\EventStore\Storage\EventStorage;
use AppBundle\Message\Event;
use AppBundle\Message\Events;
use AppBundle\MessageBus\EventBus;
use JMS\Serializer\Serializer;

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
        return $this->getMockBuilder(EventStorage::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return $this->getMockBuilder(Serializer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return EventClassMap
     */
    private function getEventClassMap()
    {
        return $this->getMockBuilder(EventClassMap::class)
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
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $storage->expects(self::exactly($events->getIterator()->count()))
            ->method('append')
            ->with($id);

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);
        $store->save($id, $events);
    }

    public function testGetEventsForForUnknownAggregateThrowsException()
    {
        self::setExpectedException(AggregateNotFoundException::class);

        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);
        $store->getEventsForAggregate(Guid::createNew());
    }

    public function testGetEventsForAggregateCallsStorage()
    {
        $id = Guid::createNew();
        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $storage->expects(self::any())
            ->method('contains')
            ->with($id->getValue())
            ->will(self::returnValue(true));

        $storage->expects(self::once())
            ->method('find')
            ->with($id->getValue())
            ->will(self::returnValue([]));

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);

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
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $event = new BookAdded($id, 'foo');

        $classMap->expects(self::any())
            ->method('getClassByEventName')
            ->with($event->getEventName())
            ->will(self::returnValue(get_class($event)));

        $serializer->expects(self::any())
            ->method('serialize')
            ->will(self::returnValue(json_encode(['id' => $event->getId(), 'title' => $event->getTitle()])));

        $serializer->expects(self::any())
            ->method('deserialize')
            ->will(self::returnValue($event));

        $eventStructure = [
            'identity' => $id->getValue(),
            'eventName' => $event->getEventName(),
            'payload' => $serializer->serialize($event, 'json'),
            'recordedOn' => date('r')
        ];

        $storage->expects(self::any())
            ->method('contains')
            ->with($id->getValue())
            ->will(self::returnValue(true));

        $storage->expects(self::once())
            ->method('find')
            ->with($id->getValue())
            ->will(self::returnValue([$eventStructure]));

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);
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
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $eventBus->expects(self::exactly($events->getIterator()->count()))
            ->method('publish')
            ->with(self::logicalOr($events->getIterator()->offsetGet(0), $events->getIterator()->offsetGet(1)));

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);
        $store->save($id, $events);
    }
}

class FirstEvent extends Event {}
class SecondEvent extends Event {}
