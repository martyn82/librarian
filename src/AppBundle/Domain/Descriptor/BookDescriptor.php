<?php

namespace AppBundle\Domain\Descriptor;

use AppBundle\Domain\Model\Authors;

trait BookDescriptor
{
    /**
     * @var Authors
     */
    private $authors;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var string
     */
    private $abstract;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @return Authors
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
