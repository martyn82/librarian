<?php

namespace AppBundle\EventSourcing\EventStore;

interface Repository
{
    /**
     * @param AggregateRoot $aggregate
     * @param int $expectedPlayhead
     */
    public function store(AggregateRoot $aggregate, $expectedPlayhead = EventStore::FIRST_VERSION);

    /**
     * @param Uuid $id
     * @return AggregateRoot
     */
    public function findById(Uuid $id);
}
