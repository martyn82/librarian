<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\EventStore\Guid;

class Book
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @var string
     */
    private $title;

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

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
