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
     * @param Uuid $id
     * @return AggregateRoot
     */
    public function findById(Uuid $id);
}
