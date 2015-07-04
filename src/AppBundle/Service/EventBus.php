<?php

namespace AppBundle\Service;

use AppBundle\EventStore\Event;

class EventBus implements EventPublisher
{
    /**
     * @var array
     */
    private $handlers = [];

    /**
     * @param string $eventClassName
     * @param EventHandler $handler
     */
    public function registerHandler($eventClassName, EventHandler $handler)
    {
        if (!array_key_exists((string) $eventClassName, $this->handlers)) {
            $this->handlers[(string) $eventClassName] = [];
        }

        $this->handlers[(string) $eventClassName][] = $handler;
    }

    /**
     * @param Event $event
     */
    public function publish(Event $event)
    {
        $eventClassName = get_class($event);

        if (!array_key_exists($eventClassName, $this->handlers)) {
            return;
        }

        foreach ($this->handlers[$eventClassName] as $handler) {
            /* @var $handler EventHandler */
            $handler->handle($event);
        }
    }
}
