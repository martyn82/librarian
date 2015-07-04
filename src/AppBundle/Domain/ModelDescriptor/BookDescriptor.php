<?php

namespace AppBundle\Domain\ModelDescriptor;

use AppBundle\Domain\Model\AuthorView;

trait BookDescriptor
{
    /**
     * @var AuthorView[]
     */
    private $authors;

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
     * @return AuthorView[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
