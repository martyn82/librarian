<?php

namespace AppBundle\EventStore\Storage;

use AppBundle\EventStore\EventDescriptor;

class MemoryEventStorage implements EventStorage
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::contains()
     */
    public function contains($identity)
    {
        return array_key_exists($identity, $this->data);
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::append()
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
     * @see \AppBundle\EventStore\Storage\EventStorage::find()
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
}
