<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\EventStore\AggregateRoot;
use AppBundle\EventStore\Uuid;

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
     * @return Book
     */
    public static function add(Uuid $id, array $authors, $title)
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

        $instance->applyChange(new BookAdded($instance->getId(), $authorsAdded, $title));
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
