<?php

namespace AppBundle\EventStore;

interface Repository
{
    /**
     * @param AggregateRoot $aggregate
     */
    public function store(AggregateRoot $aggregate);

    /**
     * @param Guid $id
     * @return AggregateRoot
     */
    public function findById(Guid $id);
}
