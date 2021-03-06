<?php

namespace AppBundle\Tests\EventSourcing\EventStore;

use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\EventSourcing\EventStore\AggregateNotFoundException;
use AppBundle\EventSourcing\EventStore\ConcurrencyException;
use AppBundle\EventSourcing\EventStore\EventClassMap;
use AppBundle\EventSourcing\EventStore\EventDescriptor;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Storage\EventStorage;
use AppBundle\EventSourcing\EventStore\Storage\MemoryEventStorage;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Event;
use AppBundle\EventSourcing\Message\Events;
use AppBundle\EventSourcing\MessageBus\EventBus;
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
     * @return EventStorage
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
        $id = Uuid::createNew();
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
            ->method('append');

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);
        $store->save($id, $events, -1);
    }

    public function testGetEventsForForUnknownAggregateThrowsException()
    {
        self::setExpectedException(AggregateNotFoundException::class);

        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);
        $store->getEventsForAggregate(Uuid::createNew());
    }

    public function testGetEventsForAggregateCallsStorage()
    {
        $id = Uuid::createNew();
        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $storage->expects(self::any())
            ->method('contains')
            ->with($id->getValue())
            ->will(self::returnValue(true));

        $storage->expects(self::atLeastOnce())
            ->method('find')
            ->with($id->getValue())
            ->will(self::returnValue([]));

        $store = new EventStore($eventBus, $storage, $serializer, $classMap);

        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $store->save($id, new Events([$event]), -1);
        $store->getEventsForAggregate($id);
    }

    public function testGetEventsForAggregateWillReturnEvents()
    {
        $id = Uuid::createNew();
        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $classMap = $this->getEventClassMap();

        $event = new BookAdded($id, [], 'foo', 'bar');

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

        $eventStructure = EventDescriptor::reconstructFromArray([
            'identity' => $id->getValue(),
            'event' => $event->getEventName(),
            'playhead' => $event->getVersion(),
            'payload' => $serializer->serialize($event, 'json'),
            'recorded' => date('r')
        ]);

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
        $id = Uuid::createNew();
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
        $store->save($id, $events, -1);
    }

    public function testSaveEventsIncreasesPlayhead()
    {
        $id = Uuid::createNew();
        $events = new Events(
            [
                new FirstEvent(),
                new SecondEvent()
            ]
        );

        $eventBus = $this->getEventBus();
        $serializer = $this->getSerializer();

        $serializer->expects(self::any())
            ->method('serialize')
            ->will(self::returnCallback(
                function ($data, $_) {
                    return json_encode(['version' => $data->getVersion()]);
                }
            ));

        $serializer->expects(self::any())
            ->method('deserialize')
            ->will(self::returnCallback(
                function ($data, $type, $_) {
                    switch ($type) {
                        case FirstEvent::class:
                            return new FirstEvent(json_decode($data, true));
                        case SecondEvent::class:
                            return new SecondEvent(json_decode($data, true));
                    }
                }
            ));

        $classMap = new EventClassMap([
            FirstEvent::class,
            SecondEvent::class
        ]);

        $store = new EventStore($eventBus, new MemoryEventStorage(), $serializer, $classMap);
        $store->save($id, $events, -1);

        $recordedEvents = $store->getEventsForAggregate($id);

        /* @var $recorded Event[] */
        $recorded = iterator_to_array($recordedEvents->getIterator());

        self::assertCount(2, $recorded);
        self::assertEquals(0, $recorded[0]->getVersion());
        self::assertEquals(1, $recorded[1]->getVersion());
    }

    public function testAppendEventForNewAggregateWithWrongPlayheadThrowsException()
    {
        self::setExpectedException(ConcurrencyException::class);

        $aggregateId = Uuid::createNew();
        $events = new Events([new FirstEvent()]);

        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $map = $this->getEventClassMap();

        $store = new EventStore($eventBus, $storage, $serializer, $map);
        $store->save($aggregateId, $events, 1);
    }

    public function testAppendEventForExistingAggregateWithWrongPlayheadThrowsException()
    {
        self::setExpectedException(ConcurrencyException::class);

        $aggregateId = Uuid::createNew();
        $events = new Events([new FirstEvent()]);

        $eventBus = $this->getEventBus();
        $storage = new MemoryEventStorage();
        $serializer = $this->getSerializer();
        $map = $this->getEventClassMap();

        $store = new EventStore($eventBus, $storage, $serializer, $map);
        $store->save($aggregateId, $events, -1);

        $store->save($aggregateId, new Events([new SecondEvent()]), 1);
    }

    public function testGetAggregateIdsCallsFindIdentitiesOnStorage()
    {
        $eventBus = $this->getEventBus();
        $storage = $this->getStorage();
        $serializer = $this->getSerializer();
        $map = $this->getEventClassMap();

        $storage->expects(self::once())
            ->method('findIdentities')
            ->will(self::returnValue(['id']));

        $store = new EventStore($eventBus, $storage, $serializer, $map);
        $store->getAggregateIds();
    }
}

class FirstEvent extends Event
{
    public function __construct(array $data = null)
    {
        $this->setVersion($data['version']);
    }
}
class SecondEvent extends Event
{
    public function __construct(array $data = null)
    {
        $this->setVersion($data['version']);
    }
}
