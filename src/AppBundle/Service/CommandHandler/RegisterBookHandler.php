<?php

namespace AppBundle\Service\CommandHandler;

use AppBundle\Repository\Books;
use AppBundle\Service\Command\RegisterBook;

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
        $this->books->add($command->getBook());
    }
}
