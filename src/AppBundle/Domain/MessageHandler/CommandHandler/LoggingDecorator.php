<?php

namespace AppBundle\Domain\MessageHandler\CommandHandler;

use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use Psr\Log\LoggerInterface;

class LoggingDecorator implements CommandHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CommandHandler
     */
    private $innerHandler;

    /**
     * @param LoggerInterface $logger
     * @param CommandHandler $innerHandler
     */
    public function __construct(LoggerInterface $logger, CommandHandler $innerHandler)
    {
        $this->logger = $logger;
        $this->innerHandler = $innerHandler;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $this->onBeforeHandle($command);
        $this->innerHandler->handle($command);
        $this->onAfterHandle($command);
    }

    /**
     * @param Command $command
     */
    private function onBeforeHandle(Command $command)
    {
        $this->logger->debug("Handle command", [var_export($command, true)]);
    }

    /**
     * @param Command $command
     */
    private function onAfterHandle(Command $command)
    {
        $this->logger->debug("Command handled", [var_export($command, true)]);
    }
}
