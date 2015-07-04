<?php

namespace AppBundle\Domain\DTO;

use AppBundle\Domain\Descriptor\BookDescriptor;
use AppBundle\EventStore\Guid;

class Book
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
