<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Event\BookRegistered;
use AppBundle\Service\EventHandler;

interface HandlesBookRegistered extends EventHandler
{
    /**
     * @param BookRegistered $event
     */
    public function handleBookRegistered(BookRegistered $event);
}
