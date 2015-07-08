<?php

namespace AppBundle\EventStore;

use AppBundle\EventStore\Storage\Storage;
use AppBundle\Message\Event;
use AppBundle\Message\Events;
use AppBundle\MessageBus\EventBus;

class EventStore
{
    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param EventBus $eventBus
     * @param Storage $storage
     */
    public function __construct(EventBus $eventBus, Storage $storage)
    {
        $this->eventBus = $eventBus;
        $this->storage = $storage;
    }

    /**
     * @param Guid $aggregateId
     * @param Events $events
     */
    public function save(Guid $aggregateId, Events $events)
    {
        foreach ($events->getIterator() as $event) {
            /* @var $event Event */
            $this->saveEvent($aggregateId, $event);
            $this->eventBus->publish($event);
        }
    }

    /**
     * @param Guid $aggregateId
     * @param Event $event
     */
    private function saveEvent(Guid $aggregateId, Event $event)
    {
        $eventData = [
            'timeStamp' => time(),
            'dateTime' => date('r'),
            'identity' => $aggregateId->getValue(),
            'eventName' => $event->getEventName(),
            'payload' => serialize($event)
        ];

        $this->storage->upsert($aggregateId->getValue(), $eventData);
    }

    /**
     * @param Guid $aggregateId
     * @return Events
     * @throws AggregateNotFoundException
     */
    public function getEventsForAggregate(Guid $aggregateId)
    {
        if (!$this->storage->contains($aggregateId->getValue())) {
            throw new AggregateNotFoundException($aggregateId);
        }

        $eventsData = $this->storage->find($aggregateId->getValue());
        $events = array_map(
            function (array $eventData) {
                return unserialize($eventData['payload']);
            },
            $eventsData
        );

        return new Events($events);
    }
}
