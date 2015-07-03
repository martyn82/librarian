<?php

namespace AppBundle\EventStore;

class EventStore
{
    /**
     * @var array
     */
    private $current = [];

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
