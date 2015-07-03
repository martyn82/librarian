<?php

namespace AppBundle\Domain\CommandHandler;

use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\Domain\Command\RegisterBook;
use AppBundle\Service\CommandHandler;
use Psr\Log\LoggerInterface;

class RegisterBookHandler implements CommandHandler
{
    /**
     * @var Books
     */
    private $books;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Books $repository
     */
    public function __construct(Books $repository)
    {
        $this->books = $repository;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RegisterBook $command
     */
    public function handle(RegisterBook $command)
    {
        $this->logHandleCommand($command);

        $book = Book::register($command->getId());
        $this->books->store($book);

        $this->logCommandHandled($command, $book);
    }

    /**
     * @param RegisterBook $command
     */
    private function logHandleCommand(RegisterBook $command)
    {
        if ($this->logger == null) {
            return;
        }

        $this->logger->debug("Handle command RegisterBook", [$command]);
    }

    /**
     * @param RegisterBook $command
     * @param Book $book
     */
    private function logCommandHandled(RegisterBook $command, Book $book)
    {
        if ($this->logger == null) {
            return;
        }

        $this->logger->debug("Command RegisterBook handled", [$book]);
    }
}
