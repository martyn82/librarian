<?php

namespace AppBundle\Message;

abstract class Event
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @return string
     */
    public function getEventName()
    {
        if ($this->eventName == null) {
            $eventClassName = get_class($this);
            $eventClassParts = explode('\\', $eventClassName);
            $this->eventName = end($eventClassParts);
        }

        return $this->eventName;
    }
}
