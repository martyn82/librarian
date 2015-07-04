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
     * @param array $authors
     * @param string $title
     */
    public function __construct(Guid $id, array $authors, $title)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->title = $title;
    }
}
