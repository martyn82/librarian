<?php

namespace AppBundle\Message;

abstract class Event
{
    /**
     * @return string
     */
    public static function getName()
    {
        $eventClassParts = explode('\\', static::class);
        return end($eventClassParts);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return self::getName();
    }
}
