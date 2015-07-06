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
     * @var Authors
     */
    private $authors;

    /**
     * @param Guid $id
     * @param Authors $authors
     * @param string $title
     */
    public function __construct(Guid $id, Authors $authors, $title)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->title = $title;

        $authorIds = array_map(
            function (AuthorView $author) {
                return $author->getId();
            },
            iterator_to_array($authors->getIterator())
        );

        $this->authorIds = new Guids($authorIds);
    }

    /**
     * @return Guid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @return Authors
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
