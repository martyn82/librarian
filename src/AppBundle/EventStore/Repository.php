<?php

namespace AppBundle\EventStore;

interface Repository
{
    /**
     * @param AggregateRoot $aggregate
     * @param int $expectedPlayhead
     */
    public function store(AggregateRoot $aggregate, $expectedPlayhead);

    /**
     * @param Guid $id
     * @return AggregateRoot
     */
    public function findById(Guid $id);
}
