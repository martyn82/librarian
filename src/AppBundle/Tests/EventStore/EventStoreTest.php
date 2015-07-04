<?php

namespace AppBundle\Tests\EventStore;

use AppBundle\EventStore\AggregateNotFoundException;
use AppBundle\EventStore\Event;
use AppBundle\EventStore\Events;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;
use AppBundle\Service\EventBus;

class EventStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveEventsForAggregateStoresEventsInCorrectOrder()
    {
        $id = Guid::createNew();
        $events = new Events(
            [
                new FirstEvent(),
                new SecondEvent()
            ]
        );

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $store = new EventStore($eventBus);
        $store->save($id, $events);

        $retrievedEvents = $store->getEventsForAggregate($id);

        $eventsIterator = $events->getIterator();
        $retrievedEventsIterator = $retrievedEvents->getIterator();

        self::assertCount($eventsIterator->count(), $retrievedEventsIterator);

        for ($i = 0; $i < $eventsIterator->count(); $i++) {
            self::assertEquals($eventsIterator->offsetGet($i), $retrievedEventsIterator->offsetGet($i));
        }
    }

    public function testGetEventsForForUnknownAggregateThrowsException()
    {
        self::setExpectedException(AggregateNotFoundException::class);

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $store = new EventStore($eventBus);
        $store->getEventsForAggregate(Guid::createNew());
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

        $eventBus = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventBus->expects(self::exactly($events->getIterator()->count()))
            ->method('publish')
            ->with(self::logicalOr($events->getIterator()->offsetGet(0), $events->getIterator()->offsetGet(1)));

        $store = new EventStore($eventBus);
        $store->save($id, $events);
    }
}

class FirstEvent extends Event {}
class SecondEvent extends Event {}
