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
    public function contains($id)
    {
        return array_key_exists($id, $this->data);
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::upsert()
     */
    public function append($id, array $data)
    {
        if (!$this->contains($id)) {
            $this->data[$id] = [];
        }

        $this->data[$id][] = $data;
        return true;
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::find()
     */
    public function find($id)
    {
        if (!$this->contains($id)) {
            return [];
        }

        return $this->data[$id];
    }
}
