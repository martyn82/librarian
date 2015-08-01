<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\Message\Event\BookReturned;
use AppBundle\EventSourcing\MessageHandler\EventHandler;

interface BookReturnedHandler extends EventHandler
{
    /**
     * @param BookReturned $event
     */
    public function onBookReturned(BookReturned $event);
}
