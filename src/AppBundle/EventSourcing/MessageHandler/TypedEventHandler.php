<?php

namespace AppBundle\EventSourcing\MessageHandler;

use AppBundle\EventSourcing\Message\Event;

trait TypedEventHandler
{
    /**
     * @param Event $event
     * @throws \InvalidArgumentException
     */
    public function on(Event $event)
    {
        $eventHandleMethod = $this->inflectHandleMethod($event);
        $this->{$eventHandleMethod}($event);
    }

    /**
     * @param Event $event
     * @return string
     * @throws \InvalidArgumentException
     */
    private function inflectHandleMethod(Event $event)
    {
        $eventHandleMethod = 'on' . $event->getEventName();

        if (!method_exists($this, $eventHandleMethod) || $event->getEventName() == null) {
            $eventClassName = get_class($event);
            throw new \InvalidArgumentException("Unable to handle event '{$eventClassName}'.");
        }

        return $eventHandleMethod;
    }
}
