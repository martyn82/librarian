<?php

namespace AppBundle\EventStore;

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
        parent::__construct(sprintf(self::$messageTemplate, $aggregateId->getValue()));
    }
}
