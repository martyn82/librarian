<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\DTO\Book;
use AppBundle\Domain\Event\BookRegistered;
use AppBundle\EventStore\Event;
use AppBundle\EventStore\Guid;
use AppBundle\Service\EventBus;

class ReadModelService implements ReadModel, HandlesBookRegistered
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @param EventBus $eventBus
     */
    public function __construct(EventBus $eventBus)
    {
        $this->storage = [];
        $eventBus->registerHandler(BookRegistered::class, $this);
    }

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
        $this->storage[$event->getId()->getValue()] = new Book($event->getId(), $event->getTitle());
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
