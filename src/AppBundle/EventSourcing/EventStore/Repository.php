<?php

namespace AppBundle\EventSourcing\EventStore;

interface Repository
{
    /**
     * @param AggregateRoot $aggregate
     * @param integer $expectedPlayHead
     */
    public function store(AggregateRoot $aggregate, $expectedPlayHead = -1);

    /**
     * @param Uuid $id
     * @return AggregateRoot
     */
    public function findById(Uuid $id);
}
