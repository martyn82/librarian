<?php

namespace AppBundle\EventSourcing\EventStore\Storage;

use AppBundle\EventSourcing\EventStore\EventDescriptor;

class MemoryEventStorage implements EventStorage
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @param string $identity
     * @return boolean
     */
    public function contains($identity)
    {
        return array_key_exists($identity, $this->data);
    }

    /**
     * @param EventDescriptor $event
     * @return boolean
     */
    public function append(EventDescriptor $event)
    {
        if (!$this->contains($event->getIdentity())) {
            $this->data[$event->getIdentity()] = [];
        }

        $this->data[$event->getIdentity()][] = $event->toArray();
        return true;
    }

    /**
     * @param string $identity
     * @return EventDescriptor[]
     */
    public function find($identity)
    {
        if (!$this->contains($identity)) {
            return [];
        }

        return array_map(
            function (array $eventData) {
                return EventDescriptor::reconstructFromArray($eventData);
            },
            $this->data[$identity]
        );
    }

    /**
     * @return string[]
     */
    public function findIdentities()
    {
        return array_keys($this->data);
    }
}
