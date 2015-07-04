<?php

namespace AppBundle\Domain\Command;

use AppBundle\Domain\Descriptor\BookDescriptor;
use AppBundle\EventStore\Guid;
use AppBundle\Service\Command;

class RegisterBook implements Command
{
    use BookDescriptor;

    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     * @param string $title
     */
    public function __construct(Guid $id, $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @return Guid
     */
    public function getId()
    {
        return $this->id;
    }
}
