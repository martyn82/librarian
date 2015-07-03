<?php

namespace AppBundle\Service\CommandHandler;

use AppBundle\Repository\Books;
use AppBundle\Service\Command\RegisterBook;
use AppBundle\Model\Book;

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
     * @param RegisterBook $command
     */
    public function handle(RegisterBook $command)
    {
        $book = Book::register($command->getId());
        $this->books->store($book);
    }
}
