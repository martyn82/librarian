<?php

namespace AppBundle\EventStore;

use AppBundle\Collections\BasicMap;
use AppBundle\Collections\Map;
use AppBundle\EventStore\Storage\EventStorage;
use AppBundle\Message\Event;
use AppBundle\Message\Events;
use AppBundle\MessageBus\EventBus;
use JMS\Serializer\Serializer;

class EventStore
{
    /**
     * @var int
     */
    const FIRST_VERSION = -1;

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
     * @var Map
     */
    private $current;

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
        $this->current = new BasicMap();
    }

    /**
     * @param Uuid $aggregateId
     * @param Events $events
     * @param int $expectedPlayhead
     * @throws ConcurrencyException
     */
    public function save(Uuid $aggregateId, Events $events, $expectedPlayhead)
    {
        $expectedPlayhead = (int) $expectedPlayhead;

        if (!$this->isValidPlayhead($aggregateId, $expectedPlayhead)) {
            throw new ConcurrencyException($expectedPlayhead, $this->current->get($aggregateId->getValue()));
        }

        $playhead = $expectedPlayhead;

        foreach ($events->getIterator() as $event) {
            /* @var $event Event */
            $playhead++;
            $event->setVersion($playhead);

            $this->saveEvent($aggregateId, $event);
            $this->current->put($aggregateId->getValue(), $playhead);
            $this->eventBus->publish($event);
        }
    }

    /**
     * @param Uuid $aggregateId
     * @param int $playhead
     * @return bool
     */
    private function isValidPlayhead(Uuid $aggregateId, $playhead)
    {
        $eventDescriptors = $this->storage->find($aggregateId->getValue());

        if (!empty($eventDescriptors)) {
            $this->current->put($aggregateId->getValue(), end($eventDescriptors)->getPlayhead());
        }

        if ($this->current->get($aggregateId->getValue()) != $playhead && $playhead != static::FIRST_VERSION) {
            return false;
        }

        return true;
    }

    /**
     * @param Uuid $aggregateId
     * @param Event $event
     */
    private function saveEvent(Uuid $aggregateId, Event $event)
    {
        $eventData = EventDescriptor::record(
            $aggregateId->getValue(),
            $event->getEventName(),
            $this->serializer->serialize($event, 'json'),
            $event->getVersion()
        );

        $this->storage->append($eventData);
    }

    /**
     * @param Uuid $aggregateId
     * @return Events
     * @throws AggregateNotFoundException
     */
    public function getEventsForAggregate(Uuid $aggregateId)
    {
        if (!$this->storage->contains($aggregateId->getValue())) {
            throw new AggregateNotFoundException($aggregateId);
        }

        $eventsData = $this->storage->find($aggregateId->getValue());

        $events = array_map(
            /**
             * @param EventDescriptor $eventData
             * @return Event
             */
            function (EventDescriptor $eventData) {
                return $this->serializer->deserialize(
                    $eventData->getPayload(),
                    $this->eventMap->getClassByEventName($eventData->getEvent()),
                    'json'
                );
            },
            $eventsData
        );

        return new Events($events);
    }
}
