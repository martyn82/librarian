<?php

namespace AppBundle\Domain\Descriptor;

interface UserDescriptor
{
    /**
     * @return string
     */
    public function getUserName();

    /**
     * @return string
     */
    public function getEmailAddress();
}
