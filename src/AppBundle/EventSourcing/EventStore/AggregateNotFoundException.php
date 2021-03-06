<?php

namespace AppBundle\EventSourcing\EventStore;

class AggregateNotFoundException extends EventStoreException
{
    /**
     * @var string
     */
    private static $messageTemplate = "Aggregate with ID '%s' not found.";

    /**
     * @param Uuid $aggregateId
     */
    public function __construct(Uuid $aggregateId)
    {
        parent::__construct(sprintf(static::$messageTemplate, $aggregateId->getValue()));
    }
}
