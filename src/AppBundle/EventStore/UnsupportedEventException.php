<?php

namespace AppBundle\EventStore;

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
        parent::__construct(sprintf(self::$messageTemplate, $eventName, $aggregateClassName));
    }
}
