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
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

class BookService implements AuthorAddedHandler, BookAddedHandler
{
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
     * @param Event $event
     * @throws \InvalidArgumentException
     */
    public function handle(Event $event)
    {
        $eventHandleMethod = 'handle' . $event->getEventName();

        if (!method_exists($this, $eventHandleMethod) || $event->getEventName() == null) {
            $eventClassName = get_class($event);
            throw new \InvalidArgumentException("Unable to handle event '{$eventClassName}'.");
        }

        $this->{$eventHandleMethod}($event);
    }

    /**
     * @see \AppBundle\Domain\Service\BookAddedHandler::handleBookAdded()
     */
    public function handleBookAdded(BookAdded $event)
    {
        $this->storage->upsert(
            $event->getId()->getValue(),
            new Book(
                $event->getId(),
                new Authors(),
                $event->getTitle(),
                $event->getVersion()
            )
        );
    }

    /**
     * @see \AppBundle\Domain\Service\AuthorAddedHandler::handleAuthorAdded()
     */
    public function handleAuthorAdded(AuthorAdded $event)
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
     * @param Guid $id
     * @return Book
     * @throws ObjectNotFoundException
     */
    public function getBook(Guid $id)
    {
        $book = $this->storage->find($id->getValue());

        if ($book === null) {
            throw new ObjectNotFoundException('Book', $id);
        }

        return $book;
    }
}
