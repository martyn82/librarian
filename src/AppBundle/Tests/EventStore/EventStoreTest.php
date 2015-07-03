<?php

namespace AppBundle\Tests\EventStore;

use AppBundle\EventStore\AggregateNotFoundException;
use AppBundle\EventStore\Event;
use AppBundle\EventStore\Events;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Guid;

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

        $store = new EventStore();
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

        $store = new EventStore();
        $store->getEventsForAggregate(Guid::createNew());
    }
}

class FirstEvent extends Event {}
class SecondEvent extends Event {}
