<?php

namespace AppBundle\Domain\ModelDescriptor;

use AppBundle\Domain\Model\Guids;
use AppBundle\EventStore\Guid;

trait BookDescriptor
{
    /**
     * @var Guids
     */
    private $authorIds;

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
     * @return Guids
     */
    public function getAuthorIds()
    {
        return $this->authorIds;
    }
}
