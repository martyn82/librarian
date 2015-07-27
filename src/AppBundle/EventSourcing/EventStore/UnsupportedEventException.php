<?php

namespace AppBundle\EventSourcing\EventStore;

class UnsupportedEventException extends EventStoreException
{
    /**
     * @var string
     */
    private static $messageTemplate = "Event '%s' not supported for aggregate '%s'.";

    /**
     * @param string $eventName
     * @param string $aggregateClassName
     */
    public function __construct($eventName, $aggregateClassName)
    {
        parent::__construct(sprintf(static::$messageTemplate, $eventName, $aggregateClassName));
    }
}
