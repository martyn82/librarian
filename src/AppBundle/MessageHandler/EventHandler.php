<?php

namespace AppBundle\MessageHandler;

use AppBundle\Message\Event;

interface EventHandler
{
    /**
     * @param Event $event
     */
    public function on(Event $event);
}
