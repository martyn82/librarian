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
     * @param Uuid $id
     * @param AddAuthor[] $authors
     * @param string $title
     */
    public function __construct(Uuid $id, array $authors, $title)
    {
        $this->id = $id;
        $this->authors = array_map(
            function (AddAuthor $author) {
                return $author;
            },
            $authors
        );
        $this->title = (string) $title;
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
}
