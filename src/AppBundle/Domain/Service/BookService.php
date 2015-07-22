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
use AppBundle\EventStore\Uuid;
use AppBundle\MessageHandler\TypedEventHandler;

class BookService implements AuthorAddedHandler, BookAddedHandler
{
    use TypedEventHandler;

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
     * @see \AppBundle\Domain\Service\BookAddedHandler::handleBookAdded()
     */
    public function onBookAdded(BookAdded $event)
    {
        $authors = array_map(
            function (\AppBundle\Domain\Model\Author $author) {
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
                $event->getVersion()
            )
        );
    }

    /**
     * @see \AppBundle\Domain\Service\AuthorAddedHandler::handleAuthorAdded()
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
     * @return Book[]
     */
    public function getAll()
    {
       return $this->storage->findAll();
    }
}
