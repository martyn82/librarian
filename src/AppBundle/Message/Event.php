<?php

namespace AppBundle\Message;

abstract class Event implements Message
{
    /**
     * @return string
     */
    public function getEventName()
    {
        $eventClassName = get_class($this);
        $eventClassParts = explode('\\', $eventClassName);
        return end($eventClassParts);
    }
}
