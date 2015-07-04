<?php

namespace AppBundle\Domain\CommandHandler;

use AppBundle\Domain\Command\RegisterBook;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\Service\Command;
use AppBundle\Service\CommandHandler;
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
        $book = Book::register($command->getId());
        $this->books->store($book);
    }
}
