<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Domain\Descriptor\BookDescriptor;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\Document;
use AppBundle\EventSourcing\ReadStore\ReadModel;

class Book extends Document implements BookDescriptor, ReadModel
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
     * @var boolean
     */
    private $available;

    /**
     * @param Uuid $id
     * @param Authors $authors
     * @param string $title
     * @param string $isbn
     * @param boolean $available
     * @param integer $version
     */
    public function __construct(Uuid $id, Authors $authors, $title, $isbn, $available, $version)
    {
        $this->id = $id;
        $this->authors = $authors;
        $this->title = (string) $title;
        $this->isbn = (string) $isbn;
        $this->available = (bool) $available;
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
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
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
            'isbn' => $this->getISBN(),
            'available' => $this->isAvailable()
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
        assert(array_key_exists('available', $data));
        assert(array_key_exists('version', $data));

        return new self(
            Uuid::deserialize($data['id']),
            Authors::deserialize($data['authors']),
            $data['title'],
            $data['isbn'],
            $data['available'],
            $data['version']
        );
    }
}
