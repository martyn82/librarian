<?php

namespace AppBundle\Domain\Descriptor;

trait BookDescriptor
{
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
}
