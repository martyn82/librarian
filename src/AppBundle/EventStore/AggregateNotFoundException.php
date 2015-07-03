<?php

namespace AppBundle\EventStore;

class AggregateNotFoundException extends EventStoreException
{
    /**
     * @var string
     */
    private static $messageTemplate = "Aggregate with ID '%s' not found.";

    /**
     * @param Guid $aggregateId
     */
    public function __construct(Guid $aggregateId)
    {
        parent::__construct(sprintf(self::$messageTemplate, $aggregateId->getValue()));
    }
}
