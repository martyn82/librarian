<?php

namespace AppBundle\Command\ReloadReadStore;

use AppBundle\Domain\Aggregate\Book as BookAggregate;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\Message\Event\BookAdded;

class Book extends BookAggregate
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
     * @param BookAdded $event
     */
    protected function applyBookAdded(BookAdded $event)
    {
        parent::applyBookAdded($event);
        $this->title = $event->getTitle();
        $this->isbn = $event->getISBN();
        $this->authors = $event->getAuthors();
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
     * @return AuthorAdded[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
