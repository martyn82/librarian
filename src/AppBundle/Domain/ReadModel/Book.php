<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\MessageHandler\EventHandler\AuthorAddedHandler;
use AppBundle\Domain\MessageHandler\EventHandler\BookAddedHandler;
use AppBundle\Domain\Model\Authors;
use AppBundle\Domain\Model\AuthorView;
use AppBundle\Domain\Model\BookView;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

class Book implements AuthorAddedHandler, BookAddedHandler
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
        $this->storage[$event->getId()->getValue()] = new BookView($event->getId(), new Authors(), $event->getTitle());
    }

    /**
     * @see \AppBundle\Domain\Service\AuthorAddedHandler::handleAuthorAdded()
     */
    public function handleAuthorAdded(AuthorAdded $event)
    {
        $oldBook = $this->getBook($event->getBookId());
        $authors = clone $oldBook->getAuthors();

        $authors->add(
            new AuthorView($event->getId(), $event->getFirstName(), $event->getLastName())
        );

        $this->storage[$event->getBookId()->getValue()] = new BookView(
            $event->getBookId(),
            $authors,
            $oldBook->getTitle()
        );
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
