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
     * @param string $resourceClass
     */
    public function __construct($resourceClass)
    {
        $this->resourceClass = $resourceClass;
        $this->view = View::create();
    }

    /**
     * @param Document $document
     * @return ViewBuilder
     */
    public function setDocument(Document $document)
    {
        $resourceClass = $this->resourceClass;

        $this->view->setData(
            $resourceClass::createFromDocument($document)
        );

        return $this;
    }

    /**
     * @param Document $document
     * @return ViewBuilder
     */
    public function setVersion(Document $document)
    {
        $this->view->setHeader('ETag', hash('sha256', $document->getId() . $document->getVersion()));
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

        $this->view->setData($resources);
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
        return $this->view;
    }
}
