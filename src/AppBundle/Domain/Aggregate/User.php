<?php

namespace AppBundle\Domain\Aggregate;

use AppBundle\Domain\Message\Event\UserCreated;
use AppBundle\EventSourcing\EventStore\AggregateRoot;
use AppBundle\EventSourcing\EventStore\Uuid;

class User extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @param Uuid $id
     * @param string $userName
     * @param string $emailAddress
     * @param string $fullName
     * @return User
     */
    public static function create(Uuid $id, $userName, $emailAddress, $fullName)
    {
        $instance = new self($id);
        $instance->applyChange(new UserCreated($id, $userName, $emailAddress, $fullName));
        return $instance;
    }

    /**
     * @param Uuid $id
     */
    public function __construct(Uuid $id)
    {
        $this->id = $id;
        parent::__construct();
    }

    /**
     * @return Uuid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @param UserCreated $event
     */
    protected function applyUserCreated(UserCreated $event)
    {
        $this->id = $event->getId();
    }
}