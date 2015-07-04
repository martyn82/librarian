<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Model\Author;
use AppBundle\Domain\Repository\Books;
use AppBundle\Message\Command;
use AppBundle\MessageHandler\CommandHandler;
use AppBundle\Domain\Model\Book;

class AddAuthorHandler implements CommandHandler
{
    /**
     * @var Books
     */
    private $books;

    /**
     * @param Books $repository
     */
    public function __construct(Books $repository)
    {
        $this->books = $repository;
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
        $book = new Book($command->getBookId());
        $book->addAuthor($command->getId(), $command->getFirstName(), $command->getLastName());
        $this->books->store($book);
    }
}
