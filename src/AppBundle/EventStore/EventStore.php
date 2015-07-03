<?php

namespace AppBundle\EventStore;

use Psr\Log\LoggerInterface;

class EventStore
{
    /**
     * @var array
     */
    private $current = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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

            $this->logEventStored($event);
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

    /**
     * @param Event $event
     */
    private function logEventStored(Event $event)
    {
        if ($this->logger == null) {
            return;
        }

        $this->logger->debug("Event stored", [$event]);
    }
}
