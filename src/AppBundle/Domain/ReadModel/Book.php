<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\Document;

class Book extends Document
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var integer
     */
    private $version;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Authors
     */
    private $authors;

    /**
     * @var string
     */
    private $isbn;

    /**
     * @param Uuid $id
     * @param Authors $authors
     * @param string $title
     * @param string $isbn
     * @param integer $version
     */
    public function __construct(Uuid $id, Authors $authors, $title, $isbn, $version)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->title = $title;
        $this->isbn = $isbn;
        $this->version = (int) $version;
    }

    /**
     * @return Uuid
     */
    final public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    final public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return Authors
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
     * @return array
     */
    public function serialize()
    {
        return [
            'id' => $this->getId()->serialize(),
            'version' => $this->getVersion(),
            'authors' => $this->getAuthors()->serialize(),
            'title' => $this->getTitle(),
            'isbn' => $this->getISBN()
        ];
    }

    /**
     * @param array $data
     * @return Book
     */
    public static function deserialize(array $data)
    {
        assert(array_key_exists('id', $data));
        assert(array_key_exists('authors', $data));
        assert(array_key_exists('title', $data));
        assert(array_key_exists('isbn', $data));
        assert(array_key_exists('version', $data));

        return new self(
            Uuid::deserialize($data['id']),
            Authors::deserialize($data['authors']),
            $data['title'],
            $data['isbn'],
            $data['version']
        );
    }
}
