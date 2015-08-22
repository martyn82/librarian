<?php

namespace AppBundle\Domain\Repository;

use AppBundle\Domain\Aggregate\User;
use AppBundle\EventSourcing\EventStore\AggregateNotFoundException;
use AppBundle\EventSourcing\EventStore\AggregateRoot;
use AppBundle\EventSourcing\EventStore\ConcurrencyException;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\EventStore\Uuid;

class Users implements Repository
{
    /**
     * @var EventStore
     */
    private $storage;

    /**
     * @param EventStore $storage
     */
    public function __construct(EventStore $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Uuid $id
     * @return User
     * @throws AggregateNotFoundException
     */
    public function findById(Uuid $id)
    {
        $events = $this->storage->getEventsForAggregate($id);

        $user = new User($id);
        $user->loadFromHistory($events);

        return $user;
    }

    /**
     * @param AggregateRoot $aggregate
     * @param integer $expectedPlayHead
     * @throws ConcurrencyException
     */
    public function store(AggregateRoot $aggregate, $expectedPlayHead = -1)
    {
        $this->storage->save($aggregate->getId(), $aggregate->getUncommittedChanges(), $expectedPlayHead);
    }
}