<?php

namespace AppBundle\EventStore\Storage;

class MemoryStorage implements Storage
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @see \AppBundle\EventStore\Storage\Storage::contains()
     */
    public function contains($id)
    {
        return array_key_exists($id, $this->data);
    }

    /**
     * @see \AppBundle\EventStore\Storage\Storage::upsert()
     */
    public function upsert($id, array $data)
    {
        if (!$this->contains($id)) {
            $this->data[$id] = [];
        }

        $this->data[$id][] = $data;
        return true;
    }

    /**
     * @see \AppBundle\EventStore\Storage\Storage::find()
     */
    public function find($id)
    {
        if (!$this->contains($id)) {
            return [];
        }

        return $this->data[$id];
    }
}
