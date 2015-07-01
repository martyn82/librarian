<?php

namespace AppBundle\Service\Command;

use AppBundle\Model\Book;

class RegisterBook implements Command
{
    /**
     * @var Book
     */
    private $book;

    /**
     * @param Book $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * @return Book
     */
    public function getBook()
    {
        return $this->book;
    }
}
