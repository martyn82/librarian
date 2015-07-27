<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class BookAdded extends Event
{
    /**
     * @Serializer\Type("AppBundle\EventStore\Uuid")
     * @var Uuid
     */
    private $id;

    /**
     * @Serializer\Type("array<AppBundle\Domain\Message\Event\AuthorAdded>")
     * @var AuthorAdded[]
     */
    private $authors;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $title;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $isbn;

    /**
     * @param Uuid $id
     * @param AuthorAdded[] $authors
     * @param string $title
     * @param string $isbn
     */
    public function __construct(Uuid $id, array $authors, $title, $isbn)
    {
        $this->id = $id;
        $this->authors = array_map(
            function (AuthorAdded $author) {
                return $author;
            },
            $authors
        );
        $this->title = $title;
        $this->isbn = $isbn;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AuthorAdded[]
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
