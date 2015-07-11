<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Domain\Storage\Document;
use AppBundle\EventStore\Guid;

class Book extends Document
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @var int
     */
    private $version;

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
     * @param int $version
     */
    public function __construct(Guid $id, Authors $authors, $title, $version)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->title = $title;
        $this->version = (int) $version;
    }

    /**
     * @return Guid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    final public function getVersion()
    {
        return $this->version;
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
