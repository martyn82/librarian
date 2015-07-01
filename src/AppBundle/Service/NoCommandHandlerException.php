<?php

namespace AppBundle\Service;

use AppBundle\Service\ServiceException;

class NoCommandHandlerException extends ServiceException
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
        parent::__construct(sprintf(self::$messageTemplate, $command));
    }
}
