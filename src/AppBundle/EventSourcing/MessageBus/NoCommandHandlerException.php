<?php

namespace AppBundle\EventSourcing\MessageBus;

use Exception;

class NoCommandHandlerException extends \Exception
{
    /**
     * @var string
     */
    private static $messageTemplate = "No handler for command class '%s'.";

    /**
     * @param string $command
     */
    public function __construct($command)
    {
        parent::__construct(sprintf(static::$messageTemplate, $command));
    }
}
