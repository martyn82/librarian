<?php

namespace AppBundle\EventSourcing\Message;

class Events implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $events;

    /**
     * @param array $events
     */
    public function __construct(array $events)
    {
        $this->events = array_map(
            function (Event $event) {
                return $event;
            },
            $events
        );
    }

    /**
     * @param Event $event
     */
    public function add(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return integer
     */
    public function size()
    {
        return count($this->events);
    }

    /**
     */
    public function clear()
    {
        $this->events = [];
    }

    /**
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->events);
    }
}
