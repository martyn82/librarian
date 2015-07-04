<?php

namespace AppBundle\Domain\CommandHandler;

use AppBundle\Service\Command;
use AppBundle\Service\CommandHandler;
use Psr\Log\LoggerInterface;

class LoggingCommandHandler implements CommandHandler
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
     * @see \AppBundle\Service\CommandHandler::handle()
     */
    public function handle(Command $command)
    {
        $this->logger->debug("Handle command", [$command]);
        $this->innerHandler->handle($command);
        $this->logger->debug("Command handled", [$command]);
    }
}
