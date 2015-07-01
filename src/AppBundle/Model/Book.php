<?php

namespace AppBundle\Model;

use Doctrine\ODM\MongoDB as DB;

class Book
{
    /**
     * @DB\Id(strategy="AUTO")
     *
     * @var int
     */
    private $id;
}
