<?php

namespace AppBundle\EventStore;

use AppBundle\Service\EventBus;

class EventStore
{
    /**
     * @var array
     */
    private $current = [];

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param Guid $aggregateId
     * @param Events $events
     */
    public function save(Guid $aggregateId, Events $events)
    {
        if (!array_key_exists($aggregateId->getValue(), $this->current)) {
            $this->current[$aggregateId->getValue()] = [];
        }

        foreach ($events->getIterator() as $event) {
            $this->current[$aggregateId->getValue()][] = $event;
            $this->eventBus->publish($event);
        }
    }

    /**
     * @param Guid $aggregateId
     * @return Events
     * @throws AggregateNotFoundException
     */
    public function getEventsForAggregate(Guid $aggregateId)
    {
        if (!array_key_exists($aggregateId->getValue(), $this->current)) {
            throw new AggregateNotFoundException($aggregateId);
        }

        return new Events($this->current[$aggregateId->getValue()]);
    }
}
