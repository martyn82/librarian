<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Message\Event;
use AppBundle\MessageHandler\EventHandler;
use Psr\Log\LoggerInterface;

class LoggingDecorator implements EventHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventHandler
     */
    private $innerEventHandler;

    /**
     * @param LoggerInterface $logger
     * @param EventHandler $eventHandler
     */
    public function __construct(LoggerInterface $logger, EventHandler $eventHandler)
    {
        $this->logger = $logger;
        $this->innerEventHandler = $eventHandler;
    }

    /**
     * @see \AppBundle\Service\EventHandler::handle()
     */
    public function on(Event $event)
    {
        $this->onBeforeHandle($event);
        $this->innerEventHandler->on($event);
        $this->onAfterHandle($event);
    }

    /**
     * @param Event $event
     */
    private function onBeforeHandle(Event $event)
    {
        $this->logger->debug("Handling event", [var_export($event, true)]);
    }

    /**
     * @param Event $event
     */
    private function onAfterHandle(Event $event)
    {
        $this->logger->debug("Event handled", [var_export($event, true)]);
    }
}
