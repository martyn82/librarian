<?php

namespace AppBundle\EventStore\Storage;

use AppBundle\EventStore\EventDescriptor;
use Doctrine\MongoDB\Collection;

class MongoDbEventStorage implements EventStorage
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var string
     */
    private $identityField;

    /**
     * @param Collection $collection
     * @param string $identityField
     */
    public function __construct(Collection $collection, $identityField)
    {
        $this->collection = $collection;
        $this->identityField = (string) $identityField;
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::contains()
     */
    public function contains($identity)
    {
        return $this->collection->count([$this->identityField => $identity], 1) > 0;
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::find()
     */
    public function find($identity)
    {
        $cursor = $this->collection->find([$this->identityField => $identity]);

        return array_map(
            function (array $eventData) {
                return EventDescriptor::reconstructFromArray($eventData);
            },
            iterator_to_array($cursor)
        );
    }

    /**
     * @see \AppBundle\EventStore\Storage\EventStorage::append()
     */
    public function append(EventDescriptor $event)
    {
        $eventData = $event->toArray();
        $result = $this->collection->insert($eventData);
        return $result !== false;
    }
}
