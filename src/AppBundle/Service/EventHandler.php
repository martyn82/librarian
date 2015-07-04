<?php

namespace AppBundle\Service;

use AppBundle\EventStore\Event;

interface EventHandler
{
    /**
     * @param Event $event
     */
    public function handle(Event $event);
}
