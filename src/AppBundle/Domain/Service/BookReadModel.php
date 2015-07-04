<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookRegistered;
use AppBundle\Domain\MessageHandler\EventHandler\AuthorAddedHandler;
use AppBundle\Domain\MessageHandler\EventHandler\BookRegisteredHandler;
use AppBundle\Domain\Model\AuthorView;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

class BookReadModel implements AuthorAddedHandler, BookRegisteredHandler
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @param Event $event
     * @throws \InvalidArgumentException
     */
    public function handle(Event $event)
    {
        $eventClassName = get_class($event);

        switch ($eventClassName) {
            case BookRegistered::class:
                $this->handleBookRegistered($event);
                break;

            case AuthorAdded::class:
                $this->handleAuthorAdded($event);
                break;

            default:
                throw new \InvalidArgumentException("Unable to handle event '{$eventClassName}'.");
        }
    }

    /**
     * @see \AppBundle\Domain\Service\BookRegisteredHandler::handleBookRegistered()
     */
    public function handleBookRegistered(BookRegistered $event)
    {
        $this->storage[$event->getId()->getValue()] = new BookView($event->getId(), [], $event->getTitle());
    }

    /**
     * @see \AppBundle\Domain\Service\AuthorAddedHandler::handleAuthorAdded()
     */
    public function handleAuthorAdded(AuthorAdded $event)
    {
        $oldBook = $this->getBook($event->getBookId());

        $authors = $oldBook->getAuthors();
        $authors[] = new AuthorView($event->getId(), $event->getFirstName(), $event->getLastName());

        $this->storage[$event->getBookId()->getValue()] = new BookView($event->getBookId(), $authors, $oldBook->getTitle());
    }

    /**
     * @param Guid $id
     * @return BookView
     * @throws ObjectNotFoundException
     */
    public function getBook(Guid $id)
    {
        if (!array_key_exists($id->getValue(), $this->storage)) {
            throw new ObjectNotFoundException('Book', $id);
        }

        return $this->storage[$id->getValue()];
    }
}
