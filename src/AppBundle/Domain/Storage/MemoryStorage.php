<?php

namespace AppBundle\Domain\Storage;

class MemoryStorage implements Storage
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @see \AppBundle\Domain\ReadModel\Storage\Storage::upsert()
     */
    public function upsert($identity, Document $data)
    {
        $this->data[$identity] = $data;
    }

    /**
     * @see \AppBundle\Domain\ReadModel\Storage\Storage::delete()
     */
    public function delete($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return;
        }

        unset($this->data[$identity]);
    }

    /**
     * @see \AppBundle\Domain\ReadModel\Storage\Storage::find()
     */
    public function find($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return null;
        }

        return $this->data[$identity];
    }
}
