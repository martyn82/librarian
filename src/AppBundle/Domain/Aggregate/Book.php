<?php

namespace AppBundle\Domain\Aggregate;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\EventSourcing\EventStore\AggregateRoot;
use AppBundle\EventSourcing\EventStore\Uuid;

class Book extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @param Uuid $id
     * @param AddAuthor[] $authors
     * @param string $title
     * @param string $isbn
     * @return Book
     */
    public static function add(Uuid $id, array $authors, $title, $isbn)
    {
        $instance = new self($id);

        $authorsAdded = array_map(
            function (AddAuthor $author) use ($instance) {
                $authorAdded = new AuthorAdded($instance->id, $author->getFirstName(), $author->getLastName());
                $instance->applyAuthorAdded($authorAdded);
                return $authorAdded;
            },
            $authors
        );

        $instance->applyChange(new BookAdded($instance->getId(), $authorsAdded, $title, $isbn));
        return $instance;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function addAuthor($firstName, $lastName)
    {
        $this->applyChange(new AuthorAdded($this->id, $firstName, $lastName));
    }

    /**
     * @param Uuid $id
     */
    public function __construct(Uuid $id)
    {
        $this->id = $id;
        parent::__construct();
    }

    /**
     * @return Uuid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @param BookAdded $event
     */
    protected function applyBookAdded(BookAdded $event)
    {
        $this->id = $event->getId();
    }

    /**
     * @param AuthorAdded $event
     */
    protected function applyAuthorAdded(AuthorAdded $event)
    {
    }
}
