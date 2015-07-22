<?php

namespace AppBundle\Controller\Resource;

use AppBundle\Domain\ReadModel\Book as BookReadModel;
use JMS\Serializer\Annotation as Serializer;

class Book
{
    /**
     * @Serializer\Type("array<AppBundle\Controller\Resource\Author>")
     * @var Author[]
     */
    private $authors;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $title;

    /**
     * @param BookReadModel $book
     * @return Book
     */
    public static function createFromReadModel(BookReadModel $book)
    {
        return new self(
            iterator_to_array($book->getAuthors()->getIterator()),
            $book->getTitle()
        );
    }

    /**
     * @param Author[] $authors
     * @param string $title
     */
    private function __construct(array $authors, $title)
    {
        $this->authors = $authors;
        $this->title = $title;
    }

    /**
     * @return Author[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
