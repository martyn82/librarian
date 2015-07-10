<?php

namespace AppBundle\EventStore;

use AppBundle\EventStore\Storage\EventStorage;
use AppBundle\Message\Event;
use AppBundle\Message\Events;
use AppBundle\MessageBus\EventBus;
use JMS\Serializer\Serializer;

class EventStore
{
    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var EventStorage
     */
    private $storage;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var EventClassMap
     */
    private $eventMap;

    /**
     * @param EventBus $eventBus
     * @param EventStorage $storage
     * @param Serializer $serializer
     * @param EventClassMap $map
     */
    public function __construct(EventBus $eventBus, EventStorage $storage, Serializer $serializer, EventClassMap $map)
    {
        $this->eventBus = $eventBus;
        $this->storage = $storage;
        $this->serializer = $serializer;
        $this->eventMap = $map;
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
            'identity' => $aggregateId->getValue(),
            'eventName' => $event->getEventName(),
            'payload' => $this->serializer->serialize($event, 'json'),
            'recordedOn' => date('r')
        ];

        $this->storage->append($aggregateId->getValue(), $eventData);
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
            /**
             * @param array $eventData
             * @return Event
             */
            function (array $eventData) {
                $className = $this->eventMap->getClassByEventName($eventData['eventName']);
                return $this->serializer->deserialize($eventData['payload'], $className, 'json');
            },
            $eventsData
        );

        return new Events($events);
    }
}
