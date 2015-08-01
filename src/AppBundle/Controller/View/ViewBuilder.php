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
     * @return ViewBuilder
     */
    public function setVersion()
    {
        $this->setVersion = true;
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
                return $resourceClass::createFromDocument($document);
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
        $this->view->setData($resourceClass::createFromDocument($document));
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
