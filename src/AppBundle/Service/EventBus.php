<?php

namespace AppBundle\Service;

use AppBundle\EventStore\Event;

class EventBus implements EventPublisher
{
    /**
     * @var array
     */
    private $eventHandlerMap = [];

    /**
     * @param array $eventHandlerMap
     */
    public function __construct(array $eventHandlerMap)
    {
        $this->eventHandlerMap = $eventHandlerMap;
    }

    /**
     * @param Event $event
     */
    public function publish(Event $event)
    {
        $eventClassName = get_class($event);

        if (!array_key_exists($eventClassName, $this->eventHandlerMap)) {
            return;
        }

        foreach ($this->eventHandlerMap[$eventClassName] as $handler) {
            /* @var $handler EventHandler */
            $handler->handle($event);
        }
    }
}
