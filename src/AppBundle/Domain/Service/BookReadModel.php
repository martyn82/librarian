<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\BookRegistered;
use AppBundle\Domain\MessageHandler\EventHandler\BookRegisteredHandler;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Event;

class BookReadModel implements BookRegisteredHandler
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @param Event $event
     */
    public function handle(Event $event)
    {
        $eventClassName = get_class($event);

        switch ($eventClassName) {
            case BookRegistered::class:
                $this->handleBookRegistered($event);
                break;
        }
    }

    /**
     * @see \AppBundle\Domain\Service\HandlesBookRegistered::handleBookRegistered()
     */
    public function handleBookRegistered(BookRegistered $event)
    {
        $this->storage[$event->getId()->getValue()] = new BookView($event->getId(), $event->getTitle());
    }

    /**
     * @see \AppBundle\Domain\Service\ReadModel::getBook()
     */
    public function getBook(Guid $id)
    {
        if (!array_key_exists($id->getValue(), $this->storage)) {
            throw new ObjectNotFoundException('Book', $id);
        }

        return $this->storage[$id->getValue()];
    }
}
