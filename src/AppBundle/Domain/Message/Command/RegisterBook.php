<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\ModelDescriptor\BookDescriptor;
use AppBundle\EventStore\Guid;
use AppBundle\Message\Command;

final class RegisterBook implements Command
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
