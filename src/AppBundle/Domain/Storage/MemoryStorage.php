<?php

namespace AppBundle\Domain\Storage;

class MemoryStorage implements Storage
{
    /**
     * @var Document[]
     */
    private $data = [];

    /**
     * @param string $identity
     * @param Document $document
     */
    public function upsert($identity, Document $document)
    {
        $this->data[$identity] = $document;
    }

    /**
     * @param string $identity
     */
    public function delete($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return;
        }

        unset($this->data[$identity]);
    }

    /**
     * @param string $identity
     * @return Document
     */
    public function find($identity)
    {
        if (!array_key_exists($identity, $this->data)) {
            return null;
        }

        return $this->data[$identity];
    }

    /**
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return Document[]
     */
    public function findAll(array $filter = [], $offset = 0, $limit = 500)
    {
        return array_values(
            array_slice($this->data, $offset, $limit)
        );
    }

    /**
     */
    public function clear()
    {
       $this->data = [];
    }
}
