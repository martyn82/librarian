<?php

namespace AppBundle\EventStore\Storage;

use Doctrine\MongoDB\Collection;

class PersistentEventStorage implements EventStorage
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::contains()
     */
    public function contains($identity)
    {
        return $this->collection->count(['identity' => $identity], 1) > 0;
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::find()
     */
    public function find($identity)
    {
        $cursor = $this->collection->find(['identity' => $identity]);
        return iterator_to_array($cursor);
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::append()
     */
    public function append($identity, array $data)
    {
        $result = $this->collection->insert($data);
        return $result !== false;
    }
}
