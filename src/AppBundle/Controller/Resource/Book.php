<?php

namespace AppBundle\Controller\Resource;

use AppBundle\Controller\Resource\Book\Author;
use AppBundle\Domain\Descriptor\BookDescriptor;
use AppBundle\Domain\ReadModel\Author as AuthorReadModel;
use AppBundle\Domain\ReadModel\Book as BookReadModel;
use JMS\Serializer\Annotation as Serializer;

class Book implements BookDescriptor
{
    /**
     * @Serializer\SerializedName("_id")
     * @Serializer\Type("string")
     * @var string
     */
    private $id;

    /**
     * @Serializer\Type("array<AppBundle\Controller\Resource\Book\Author>")
     * @var Author[]
     */
    private $authors;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $title;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $isbn;

    /**
     * @Serializer\Type("boolean")
     * @var boolean
     */
    private $available;

    /**
     * @param BookReadModel $book
     * @return Book
     */
    public static function createFromDocument(BookReadModel $book)
    {
        $authors = array_map(
            function (AuthorReadModel $author) {
                return Author::createFromDocument($author);
            },
            iterator_to_array($book->getAuthors()->getIterator())
        );

        return new self(
            $book->getId()->getValue(),
            $authors,
            $book->getTitle(),
            $book->getISBN(),
            $book->isAvailable()
        );
    }

    /**
     * @param string $id
     * @param Author[] $authors
     * @param string $title
     * @param string $isbn
     * @param boolean $available
     */
    private function __construct($id, array $authors, $title, $isbn, $available)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->title = $title;
        $this->isbn = $isbn;
        $this->available = $available;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return string
     */
    public function getISBN()
    {
        return $this->isbn;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }
}
