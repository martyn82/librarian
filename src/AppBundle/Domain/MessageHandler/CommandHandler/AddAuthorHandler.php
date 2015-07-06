<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Model\Author;
use AppBundle\EventStore\Repository;
use AppBundle\Message\Command;
use AppBundle\MessageHandler\CommandHandler;

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
     * @see \AppBundle\MessageHandler\CommandHandler::handle()
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
        $book = $this->repository->findById($command->getBookId());
        $book->addAuthor($command->getId(), $command->getFirstName(), $command->getLastName());
        $this->repository->store($book);
    }
}
