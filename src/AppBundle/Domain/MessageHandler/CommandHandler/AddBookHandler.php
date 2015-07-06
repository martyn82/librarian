<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\Repository;
use AppBundle\Message\Command;
use AppBundle\MessageHandler\CommandHandler;
use Psr\Log\LoggerInterface;

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
     * @see \AppBundle\MessageHandler\CommandHandler::handle()
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
        $book = Book::add($command->getId(), $command->getTitle());
        $this->repository->store($book);
    }
}
