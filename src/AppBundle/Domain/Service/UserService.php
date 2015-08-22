<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\UserCreated;
use AppBundle\Domain\MessageHandler\EventHandler\UserCreatedHandler;
use AppBundle\Domain\ReadModel\User;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\MessageHandler\TypedEventHandler;
use AppBundle\EventSourcing\ReadStore\Storage;

class UserService implements UserCreatedHandler
{
    use TypedEventHandler;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param UserCreated $event
     */
    public function onUserCreated(UserCreated $event)
    {
        $this->storage->upsert(
            $event->getId()->getValue(),
            new User(
                $event->getId(),
                $event->getUserName(),
                $event->getEmailAddress(),
                $event->getVersion()
            )
        );
    }

    /**
     * @param Uuid $id
     * @return User
     * @throws ObjectNotFoundException
     */
    public function getUser(Uuid $id)
    {
        $user = $this->storage->find($id->getValue());

        if ($user == null) {
            throw new ObjectNotFoundException('User', $id);
        }

        return $user;
    }

    /**
     * @param string $emailAddress
     * @return User
     */
    public function getUserByEmailAddress($emailAddress)
    {
        $users = $this->storage->findBy(['emailAddress' => $emailAddress], 0, 1);

        if (count($users) == 0) {
            return null;
        }

        return reset($users);
    }
}