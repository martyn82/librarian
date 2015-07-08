<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Model\AuthorView;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Command;
use AppBundle\MessageBus\CommandBus;

class BookService
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var Book
     */
    private $readModel;

    /**
     * @param Book $readModel
     * @param CommandBus $commandBus
     */
    public function __construct(Book $readModel, CommandBus $commandBus)
    {
        $this->readModel = $readModel;
        $this->commandBus = $commandBus;
    }

    /**
     * @param Guid $id
     * @return BookView
     */
    public function getBook(Guid $id)
    {
        return $this->readModel->getBook($id);
    }

    /**
     * @param Command $command
     */
    public function execute(Command $command)
    {
        $this->commandBus->handle($command);
    }
}
