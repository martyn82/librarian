<?php

namespace AppBundle\Domain\Descriptor;

interface BookDescriptor
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getISBN();

    /**
     * @return mixed
     */
    public function getAuthors();

    /**
     * @return boolean
     */
    public function isAvailable();
}
