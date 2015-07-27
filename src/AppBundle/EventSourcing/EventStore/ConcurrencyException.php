<?php

namespace AppBundle\EventSourcing\EventStore;

class ConcurrencyException extends EventStoreException
{
    /**
     * @var string
     */
    private static $messageTemplate = "Playhead was expected to be '%d' but was '%d'.";

    /**
     * @param integer $expectedPlayHead
     * @param integer $actualPlayHead
     */
    public function __construct($expectedPlayHead, $actualPlayHead)
    {
        parent::__construct(sprintf(static::$messageTemplate, $expectedPlayHead, $actualPlayHead));
    }
}
