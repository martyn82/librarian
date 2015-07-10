<?php

namespace AppBundle\EventStore;

use AppBundle\Collections\BasicMap;
use AppBundle\Collections\Map;
use AppBundle\Message\Event;

class EventClassMap
{
    /**
     * @var Map
     */
    private $innerMap;

    /**
     * @param array $eventClasses
     */
    public function __construct(array $eventClasses)
    {
        $this->innerMap = new BasicMap();

        array_walk(
            $eventClasses,
            function ($eventClass) {
                $this->add($eventClass);
            }
        );
    }

    /**
     * @param string $eventClass
     */
    private function add($eventClass)
    {
        $this->innerMap->put(
            $eventClass::getName(),
            $eventClass
        );
    }

    /**
     * @param string $key
     * @return string
     */
    public function getClassByEventName($key)
    {
        return $this->innerMap->get($key);
    }
}
