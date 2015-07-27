<?php

namespace AppBundle\EventSourcing\EventStore;

use AppBundle\EventSourcing\Message\Event;
use AppBundle\EventSourcing\Message\Events;

abstract class AggregateRoot
{
    /**
     * @var Events
     */
    private $changes;

    /**
     */
    protected function __construct()
    {
        $this->changes = new Events([]);
    }

    /**
     * @return Uuid
     */
    abstract public function getId();

    /**
     * @return Events
     */
    public function getUncommittedChanges()
    {
        return $this->changes;
    }

    /**
     */
    public function markChangesCommitted()
    {
        $this->changes->clear();
    }

    /**
     * @param Events $history
     */
    public function loadFromHistory(Events $history)
    {
        foreach ($history->getIterator() as $event) {
            $this->internalApplyChange($event, false);
        }
    }

    /**
     * @param Event $event
     * @throws UnsupportedEventException
     */
    protected function apply(Event $event)
    {
        $applyMethod = 'apply' . $event->getEventName();

        if (!method_exists($this, $applyMethod)) {
            throw new UnsupportedEventException($event->getEventName(), get_class($this));
        }

        static::$applyMethod($event);
    }

    /**
     * @param Event $event
     */
    protected function applyChange(Event $event)
    {
        $this->internalApplyChange($event, true);
    }

    /**
     * @param Event $event
     * @param boolean $isNew
     */
    private function internalApplyChange(Event $event, $isNew)
    {
        $this->apply($event);

        if ((bool) $isNew) {
            $this->changes->add($event);
        }
    }
}
