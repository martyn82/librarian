<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\ReturnBook;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use AppBundle\EventSourcing\MessageHandler\TypedCommandHandler;

class ReturnBookHandler implements CommandHandler
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
     * @param ReturnBook $command
     */
    public function handleReturnBook(ReturnBook $command)
    {
        /* @var $book \AppBundle\Domain\Aggregate\Book */
        $book = $this->repository->findById($command->getId());
        $book->checkIn();
        $this->repository->store($book, $command->getVersion());
    }
}