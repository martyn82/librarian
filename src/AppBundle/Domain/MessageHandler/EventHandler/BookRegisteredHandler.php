<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\Message\Event\BookRegistered;
use AppBundle\MessageHandler\EventHandler;

interface BookRegisteredHandler extends EventHandler
{
    /**
     * @param BookRegistered $event
     */
    public function handleBookRegistered(BookRegistered $event);
}
