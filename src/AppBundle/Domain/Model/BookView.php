<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\ModelDescriptor\BookDescriptor;
use AppBundle\EventStore\Guid;

class BookView
{
    use BookDescriptor;

    /**
     * @var Guid
     */
    private $id;

    /**
     * @var AuthorView[]
     */
    private $authors = [];

    /**
     * @param Guid $id
     * @param AuthorView[] $authors
     * @param string $title
     */
    public function __construct(Guid $id, array $authors, $title)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->authorIds = array_map(
            function (AuthorView $author) {
                return $author->getId();
            },
            $authors
        );
        $this->title = $title;
    }

    /**
     * @return Guid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @return AuthorView[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
