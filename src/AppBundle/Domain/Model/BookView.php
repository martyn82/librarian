<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\ModelDescriptor\BookDescriptor;
use AppBundle\EventStore\Guid;

class BookView
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
}
