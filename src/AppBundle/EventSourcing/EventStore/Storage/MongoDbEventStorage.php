<?php

namespace AppBundle\EventSourcing\EventStore\Storage;

use AppBundle\EventSourcing\EventStore\EventDescriptor;
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
     * @param string $identity
     * @return bool
     */
    public function contains($identity)
    {
        return $this->collection->count([$this->identityField => $identity], 1) > 0;
    }

    /**
     * @param string $identity
     * @return EventDescriptor[]
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
     * @param EventDescriptor $event
     * @return bool
     */
    public function append(EventDescriptor $event)
    {
        $eventData = $event->toArray();
        $result = $this->collection->insert($eventData);
        return $result !== false;
    }

    /**
     * @return string[]
     */
    public function findIdentities()
    {
        return $this->collection->distinct($this->identityField)->toArray();
    }
}
