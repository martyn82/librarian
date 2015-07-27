<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;

class AddAuthorHandler implements CommandHandler
{
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
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $this->handleAddAuthor($command);
    }

    /**
     * @param AddAuthor $command
     */
    private function handleAddAuthor(AddAuthor $command)
    {
        $book = $this->repository->findById($command->getId());
        $book->addAuthor($command->getFirstName(), $command->getLastName());
        $this->repository->store($book, $command->getVersion());
    }
}
