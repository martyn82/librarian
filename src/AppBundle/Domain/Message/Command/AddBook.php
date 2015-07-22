<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\Domain\Model\Author;
use AppBundle\Domain\Model\BookView;
use AppBundle\EventStore\Uuid;
use AppBundle\Message\Command;

final class AddBook implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var Author[]
     */
    private $authors;

    /**
     * @var string
     */
    private $title;

    /**
     * @param Uuid $id
     * @param Author[] $authors
     * @param string $title
     */
    public function __construct(Uuid $id, array $authors, $title)
    {
        $this->id = $id;
        $this->authors = $authors;
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
     * @return Author[]
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
