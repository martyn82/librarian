<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\MessageHandler\EventHandler\AuthorAddedHandler;
use AppBundle\Domain\MessageHandler\EventHandler\BookAddedHandler;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Storage\Storage;
use AppBundle\EventSourcing\EventStore\Uuid;

class BookService implements AuthorAddedHandler, BookAddedHandler
{
    use \AppBundle\EventSourcing\MessageHandler\TypedEventHandler;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param BookAdded $event
     */
    public function onBookAdded(BookAdded $event)
    {
        $authors = array_map(
            function (AuthorAdded $author) {
                return new Author($author->getFirstName(), $author->getLastName());
            },
            $event->getAuthors()
        );

        $this->storage->upsert(
            $event->getId()->getValue(),
            new Book(
                $event->getId(),
                new Authors($authors),
                $event->getTitle(),
                $event->getISBN(),
                $event->getVersion()
            )
        );
    }

    /**
     * @param AuthorAdded $event
     * @throws ObjectNotFoundException
     */
    public function onAuthorAdded(AuthorAdded $event)
    {
        $oldBook = $this->getBook($event->getId());
        $authors = clone $oldBook->getAuthors();

        $authors->add(
            new Author($event->getFirstName(), $event->getLastName())
        );

        $this->storage->upsert(
            $event->getId()->getValue(),
            new Book(
                $event->getId(),
                $authors,
                $oldBook->getTitle(),
                $oldBook->getISBN(),
                $event->getVersion()
            )
        );
    }

    /**
     * @param Uuid $id
     * @return Book
     * @throws ObjectNotFoundException
     */
    public function getBook(Uuid $id)
    {
        $book = $this->storage->find($id->getValue());

        if ($book === null) {
            throw new ObjectNotFoundException('Book', $id);
        }

        return $book;
    }

    /**
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return Book[]
     */
    public function getAll(array $filter = [], $offset = 0, $limit = 500)
    {
       return (array)$this->storage->findAll($filter, $offset, $limit);
    }
}
