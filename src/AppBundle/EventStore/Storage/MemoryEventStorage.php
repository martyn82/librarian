<?php

namespace AppBundle\EventStore\Storage;

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
     * @see \AppBundle\EventStore\Storage\EventStorage::upsert()
     */
    public function append($identity, array $data)
    {
        if (!$this->contains($identity)) {
            $this->data[$identity] = [];
        }

        $this->data[$identity][] = $data;
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

        return $this->data[$identity];
    }
}
