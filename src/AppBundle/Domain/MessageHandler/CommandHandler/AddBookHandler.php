<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use AppBundle\EventSourcing\MessageHandler\TypedCommandHandler;

class AddBookHandler implements CommandHandler
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
     * @param AddBook $command
     */
    private function handleAddBook(AddBook $command)
    {
        $book = Book::add($command->getId(), $command->getAuthors(), $command->getTitle(), $command->getISBN());
        $this->repository->store($book);
    }
}
