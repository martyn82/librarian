<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Aggregate\User;
use AppBundle\Domain\Message\Command\CreateUser;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use AppBundle\EventSourcing\MessageHandler\TypedCommandHandler;

class CreateUserHandler implements CommandHandler
{
    use TypedCommandHandler;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateUser $command
     */
    public function handleCreateUser(CreateUser $command)
    {
        $user = User::create(
            $command->getId(),
            $command->getUserName(),
            $command->getEmailAddress(),
            $command->getFullName()
        );
        $this->repository->store($user);
    }
}