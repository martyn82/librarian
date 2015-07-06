<?php

namespace AppBundle\Domain\ModelDescriptor;

use AppBundle\Domain\Model\AuthorView;

trait BookDescriptor
{
    /**
     * @var array
     */
    private $authors = [];

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
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
