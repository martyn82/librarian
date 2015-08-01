<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\Message\Event\BookCheckedOut;
use AppBundle\Domain\Message\Event\BookReturned;
use AppBundle\Domain\MessageHandler\EventHandler\AuthorAddedHandler;
use AppBundle\Domain\MessageHandler\EventHandler\BookAddedHandler;
use AppBundle\Domain\MessageHandler\EventHandler\BookCheckedOutHandler;
use AppBundle\Domain\MessageHandler\EventHandler\BookReturnedHandler;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\MessageHandler\TypedEventHandler;
use AppBundle\EventSourcing\ReadStore\Storage;

class BookService implements AuthorAddedHandler, BookAddedHandler, BookCheckedOutHandler, BookReturnedHandler
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
                true,
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
        $current = $this->getBook($event->getId());
        $authors = clone $current->getAuthors();

        $authors->add(
            new Author($event->getFirstName(), $event->getLastName())
        );

        $this->storage->upsert(
            $event->getId()->getValue(),
            new Book(
                $event->getId(),
                $authors,
                $current->getTitle(),
                $current->getISBN(),
                $current->isAvailable(),
                $event->getVersion()
            )
        );
    }

    /**
     * @param BookCheckedOut $event
     */
    public function onBookCheckedOut(BookCheckedOut $event)
    {
        $current = $this->getBook($event->getId());

        $this->storage->upsert(
            $event->getId()->getValue(),
            new Book(
                $event->getId(),
                $current->getAuthors(),
                $current->getTitle(),
                $current->getISBN(),
                false,
                $event->getVersion()
            )
        );
    }

    /**
     * @param BookReturned $event
     */
    public function onBookReturned(BookReturned $event)
    {
        $current = $this->getBook($event->getId());

        $this->storage->upsert(
            $event->getId()->getValue(),
            new Book(
                $event->getId(),
                $current->getAuthors(),
                $current->getTitle(),
                $current->getISBN(),
                true,
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
     * @param integer $page
     * @param integer $size
     * @return Book[]
     */
    public function getAll($page = 1, $size = 500)
    {
        $page = max((int)$page, 1);
        $size = min((int)$size, 500);
        $offset = ($page - 1) * $size;

        return (array)$this->storage->findAll($offset, $size);
    }
}
