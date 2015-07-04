<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Model\BookView;
use AppBundle\EventStore\Guid;

interface ReadModel
{
    /**
     * @param Guid $id
     * @return BookView
     * @throws ObjectNotFoundException
     */
    public function getBook(Guid $id);
}
