<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\RegisterBook;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\Message\Command;
use AppBundle\MessageHandler\CommandHandler;
use Psr\Log\LoggerInterface;

class RegisterBookHandler implements CommandHandler
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
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $this->handleRegisterBook($command);
    }

    /**
     * @param RegisterBook $command
     */
    private function handleRegisterBook(RegisterBook $command)
    {
        $book = Book::register($command->getId(), $command->getTitle());
        $this->books->store($book);
    }
}
