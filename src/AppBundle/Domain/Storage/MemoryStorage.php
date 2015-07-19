<?php

namespace AppBundle\Domain\Storage;

class MemoryStorage implements Storage
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @see \AppBundle\Domain\Storage\Storage::upsert()
     */
    public function upsert($identity, Document $document)
    {
        $this->data[$identity] = $document;
    }

    /**
     * @see \AppBundle\Domain\Storage\Storage::delete()
     */
    public function delete($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return;
        }

        unset($this->data[$identity]);
    }

    /**
     * @see \AppBundle\Domain\Storage\Storage::find()
     */
    public function find($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return null;
        }

        return $this->data[$identity];
    }

    /**
     * @see \AppBundle\Domain\Storage\Storage::findAll()
     */
    public function findAll()
    {
        return array_values($this->data);
    }
}
