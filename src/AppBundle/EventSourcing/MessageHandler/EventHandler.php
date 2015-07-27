<?php

namespace AppBundle\EventSourcing\MessageHandler;

use AppBundle\EventSourcing\Message\Event;

interface EventHandler
{
    /**
     * @param Event $event
     */
    public function on(Event $event);
}
