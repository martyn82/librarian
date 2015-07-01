<?php

namespace AppBundle\Data\Repository;

use AppBundle\Model\Book;
use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\DiExtraBundle\Annotation as DI;

class Books implements Repository
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Book $book
     */
    public function add(Book $book)
    {
        $this->manager->persist($book);
    }
}
