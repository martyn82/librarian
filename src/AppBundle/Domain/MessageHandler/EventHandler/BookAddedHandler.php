<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\MessageHandler\EventHandler;

interface BookAddedHandler extends EventHandler
{
    /**
     * @param BookAdded $event
     */
    public function handleBookAdded(BookAdded $event);
}
