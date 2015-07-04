<?php

namespace AppBundle\Service;

use AppBundle\EventStore\Event;

interface EventPublisher
{
    /**
     * @param Event $event
     */
    public function publish(Event $event);
}
