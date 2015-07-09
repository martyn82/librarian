<?php

namespace AppBundle\EventStore\Storage;

use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Connection;

class PersistentEventStorage implements EventStorage
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Connection $connection
     * @param string $type
     */
    public function __construct(Connection $connection, $type)
    {
        $this->connection = $connection;
        $this->collection = $connection->selectCollection('events', $type);
    }

    /**
     */
    public function __destruct()
    {
        $this->connection->close();
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
        $result = $this->collection->upsert(['identity' => $identity], $data);
        return $result !== false;
    }
}
