<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\EventStore\Uuid;
use AppBundle\Message\Command;

final class AddBook implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var AddAuthor[]
     */
    private $authors;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @param Uuid $id
     * @param AddAuthor[] $authors
     * @param string $title
     * @param string $isbn
     */
    public function __construct(Uuid $id, array $authors, $title, $isbn)
    {
        $this->id = $id;
        $this->authors = array_map(
            function (AddAuthor $author) {
                return $author;
            },
            $authors
        );
        $this->title = (string) $title;
        $this->isbn = (string) $isbn;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AddAuthor[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getISBN()
    {
        return $this->isbn;
    }
}
