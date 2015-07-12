<?php

namespace AppBundle\EventStore;

class ConcurrencyException extends EventStoreException
{
    /**
     * @var string
     */
    private static $messageTemplate = "Playhead was expected to be '%d' but was '%d'.";

    /**
     * @param int $expectedPlayhead
     * @param int $actualPlayhead
     */
    public function __construct($expectedPlayhead, $actualPlayhead)
    {
        parent::__construct(sprintf(static::$messageTemplate, $expectedPlayhead, $actualPlayhead));
    }
}
