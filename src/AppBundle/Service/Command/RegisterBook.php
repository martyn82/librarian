<?php

namespace AppBundle\Service\Command;

use AppBundle\Model\Book;
use AppBundle\EventStore\Guid;

class RegisterBook implements Command
{
    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     */
    public function __construct(Guid $id)
    {
        $this->id = $id;
    }

    /**
     * @return Guid
     */
    public function getId()
    {
        return $this->id;
    }
}
