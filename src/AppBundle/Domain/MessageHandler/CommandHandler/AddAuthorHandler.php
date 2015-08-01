<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use AppBundle\EventSourcing\MessageHandler\TypedCommandHandler;

class AddAuthorHandler implements CommandHandler
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
     * @param AddAuthor $command
     */
    private function handleAddAuthor(AddAuthor $command)
    {
        /* @var $book \AppBundle\Domain\Aggregate\Book */
        $book = $this->repository->findById($command->getId());
        $book->addAuthor($command->getFirstName(), $command->getLastName());
        $this->repository->store($book, $command->getVersion());
    }
}
