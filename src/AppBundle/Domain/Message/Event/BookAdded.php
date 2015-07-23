<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\Domain\Model\Author;
use AppBundle\EventStore\Uuid;
use AppBundle\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class BookAdded extends Event
{
    /**
     * @Serializer\Type("AppBundle\EventStore\Uuid")
     * @var Uuid
     */
    private $id;

    /**
     * @Serializer\Type("array<AppBundle\Domain\Model\AuthorAdded>")
     * @var AuthorAdded[]
     */
    private $authors;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $title;

    /**
     * @param Uuid $id
     * @param AuthorAdded[] $authors
     * @param string $title
     */
    public function __construct(Uuid $id, array $authors, $title)
    {
        $this->id = $id;
        $this->authors = array_map(
            function (AuthorAdded $author) {
                return $author;
            },
            $authors
        );
        $this->title = $title;
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
}
