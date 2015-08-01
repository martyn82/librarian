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
     */
    public function handleCheckoutBook(CheckOutBook $command)
    {
        /* @var $book \AppBundle\Domain\Aggregate\Book */
        $book = $this->repository->findById($command->getId());
        $book->checkout();
        $this->repository->store($book, $command->getVersion());
    }
}