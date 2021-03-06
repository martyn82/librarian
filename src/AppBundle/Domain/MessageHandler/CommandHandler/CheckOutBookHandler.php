<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\CheckOutBook;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use AppBundle\EventSourcing\MessageHandler\TypedCommandHandler;

class CheckOutBookHandler implements CommandHandler
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
     * @param CheckOutBook $command
     * @throws \AppBundle\Domain\Aggregate\BookUnavailableException
     */
    public function handleCheckOutBook(CheckOutBook $command)
    {
        /* @var $book \AppBundle\Domain\Aggregate\Book */
        $book = $this->repository->findById($command->getId());
        $book->checkOut($command->getUserId());
        $this->repository->store($book, $command->getVersion());
    }
}