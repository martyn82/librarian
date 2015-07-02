<?php

namespace AppBundle\EventStore;

use AppBundle\EventStore\Events;
use AppBundle\Message\Event;
use AppBundle\Model\Guid;

abstract class AggregateRoot
{
    /**
     * @var Events
     */
    private $changes;

    /**
     */
    public function __construct()
    {
        $this->changes = new Events([]);
    }

    /**
     * @return Guid
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
        $eventClassName = get_class($event);
        $eventClassParts = explode('\\', $eventClassName);
        $eventName = end($eventClassParts);
        $applyMethod = 'apply' . $eventName;

        if (!method_exists($this, $applyMethod)) {
            throw new UnsupportedEventException($eventName, get_class($this));
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
     * @param bool $isNew
     */
    private function internalApplyChange(Event $event, $isNew)
    {
        $this->apply($event);

        if ((bool) $isNew) {
            $this->changes->add($event);
        }
    }
}
