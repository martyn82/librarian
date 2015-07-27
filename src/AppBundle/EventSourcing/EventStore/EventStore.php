<?php

namespace AppBundle\EventSourcing\EventStore;

use AppBundle\Collections\BasicMap;
use AppBundle\Collections\Map;
use AppBundle\EventSourcing\EventStore\Storage\EventStorage;
use AppBundle\EventSourcing\Message\Event;
use AppBundle\EventSourcing\Message\Events;
use AppBundle\EventSourcing\MessageBus\EventBus;
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
     * @param integer $expectedPlayHead
     * @throws ConcurrencyException
     */
    public function save(Uuid $aggregateId, Events $events, $expectedPlayHead)
    {
        $expectedPlayHead = (int)$expectedPlayHead;

        if (!$this->isValidPlayHead($aggregateId, $expectedPlayHead)) {
            throw new ConcurrencyException($expectedPlayHead, $this->current->get($aggregateId->getValue()));
        }

        $playHead = $expectedPlayHead;

        foreach ($events->getIterator() as $event) {
            /* @var $event Event */
            $playHead++;
            $event->setVersion($playHead);

            $this->saveEvent($aggregateId, $event);
            $this->current->put($aggregateId->getValue(), $playHead);
            $this->eventBus->publish($event);
        }
    }

    /**
     * @param Uuid $aggregateId
     * @param integer $playHead
     * @return boolean
     */
    private function isValidPlayHead(Uuid $aggregateId, $playHead)
    {
        $eventDescriptors = $this->storage->find($aggregateId->getValue());

        if (!empty($eventDescriptors)) {
            $this->current->put($aggregateId->getValue(), end($eventDescriptors)->getPlayhead());
        }

        if ($this->current->get($aggregateId->getValue()) != $playHead && $playHead != -1) {
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

    /**
     * @return Uuid[]
     */
    public function getAggregateIds()
    {
        return array_map(
            function ($identity) {
                return Uuid::createFromValue($identity);
            },
            $this->storage->findIdentities()
        );
    }
}
