<?php

namespace AppBundle\Domain\Command;

use AppBundle\EventStore\Guid;
use AppBundle\Service\Command;

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
