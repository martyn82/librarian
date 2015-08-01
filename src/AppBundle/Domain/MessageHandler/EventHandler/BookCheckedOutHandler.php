<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\Message\Event\BookCheckedOut;
use AppBundle\EventSourcing\MessageHandler\EventHandler;

interface BookCheckedOutHandler extends EventHandler
{
    /**
     * @param BookCheckedOut $event
     */
    public function onBookCheckedOut(BookCheckedOut $event);
}
