<?php

namespace AppBundle\Controller\View;

use AppBundle\EventSourcing\ReadStore\Document;
use FOS\RestBundle\View\View;

class ViewBuilder
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var string
     */
    private $resourceClass;

    /**
     * @var Document
     */
    private $singleDocument;

    /**
     * @var boolean
     */
    private $setVersion;

    /**
     * @param string $resourceClass
     */
    public function __construct($resourceClass)
    {
        $this->resourceClass = $resourceClass;
        $this->setVersion = false;
        $this->view = View::create();
    }

    /**
     * @param Document $document [optional]
     * @return ViewBuilder
     */
    public function setVersion(Document $document = null)
    {
        $this->setVersion = true;

        if ($document != null) {
            $this->singleDocument = $document;
        }

        return $this;
    }

    /**
     * @param Document[] $documents
     * @return ViewBuilder
     */
    public function setDocuments(array $documents)
    {
        $resourceClass = $this->resourceClass;
        $resources = array_map(
            function (Document $document) use ($resourceClass) {
                return $resourceClass::createFromReadModel($document);
            },
            $documents
        );

        $this->singleDocument = null;
        $this->view->setData($resources);
        return $this;
    }

    /**
     * @param Document $document
     * @return ViewBuilder
     */
    public function setDocument(Document $document)
    {
        $this->singleDocument = $document;
        $resourceClass = $this->resourceClass;
        $this->view->setData($resourceClass::createFromReadModel($document));
        return $this;
    }

    /**
     * @param string $location
     * @return ViewBuilder
     */
    public function setLocation($location)
    {
        $this->view->setHeader('Location', $location);
        return $this;
    }

    /**
     * @param integer $statusCode
     * @return ViewBuilder
     */
    public function setStatus($statusCode)
    {
        $this->view->setStatusCode($statusCode);
        return $this;
    }

    /**
     * @return View
     */
    public function build()
    {
        if ($this->setVersion && $this->singleDocument != null) {
            $this->view->setHeader(
                'ETag',
                hash('sha256', $this->singleDocument->getId() . $this->singleDocument->getVersion())
            );
        }

        return $this->view;
    }
}
