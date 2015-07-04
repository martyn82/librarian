<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\DTO\Book;
use AppBundle\EventStore\Guid;

interface ReadModel
{
    /**
     * @param Guid $id
     * @return Book
     * @throws ObjectNotFoundException
     */
    public function getBook(Guid $id);
}
