<?php

namespace AppBundle\Command\ReloadReadStore;

use AppBundle\Domain\Aggregate\Book as BookAggregate;
use AppBundle\Domain\Descriptor\BookDescriptor;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;
use AppBundle\Domain\Message\Event\BookCheckedOut;

class Book extends BookAggregate implements BookDescriptor
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @var AuthorAdded[]
     */
    private $authors;

    /**
     * @var boolean
     */
    private $available;

    /**
     * @param BookAdded $event
     */
    protected function applyBookAdded(BookAdded $event)
    {
        parent::applyBookAdded($event);
        $this->title = $event->getTitle();
        $this->isbn = $event->getISBN();
        $this->authors = $event->getAuthors();
        $this->available = true;
    }

    /**
     * @param AuthorAdded $event
     */
    protected function applyAuthorAdded(AuthorAdded $event)
    {
        parent::applyAuthorAdded($event);
        $this->authors[] = $event;
    }

    /**
     * @param BookCheckedOut $event
     */
    protected function applyBookCheckedOut(BookCheckedOut $event)
    {
        parent::applyBookCheckedOut($event);
        $this->available = false;
    }

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
    public function getISBN()
    {
        return $this->isbn;
    }

    /**
     * @return AuthorAdded[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }
}
