<?php

namespace AppBundle\Domain\ModelDescriptor;

use AppBundle\Domain\Model\AuthorView;
use AppBundle\EventStore\Guid;

trait BookDescriptor
{
    /**
     * @var Guid[]
     */
    private $authorIds = [];

    /**
     * @var string
     */
    private $title;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Guid[]
     */
    public function getAuthorIds()
    {
        return $this->authorIds;
    }
}
