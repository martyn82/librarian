<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\Repository;
use AppBundle\Message\Command;
use AppBundle\MessageHandler\CommandHandler;

class AddBookHandler implements CommandHandler
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
        $this->handleAddBook($command);
    }

    /**
     * @param AddBook $command
     */
    private function handleAddBook(AddBook $command)
    {
        $book = Book::add($command->getId(), $command->getAuthors(), $command->getTitle());
        $this->repository->store($book);
    }
}
