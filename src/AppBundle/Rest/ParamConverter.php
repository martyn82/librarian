<?php

namespace AppBundle\Rest;

use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class ParamConverter implements ParamConverterInterface
{
    /**
     * @var string[]
     */
    private static $parameters = [
        'book',
        'id',
        'version'
    ];

    /**
     * @var BookService
     */
    private $bookService;

    /**
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * @param ParamConfiguration $configuration
     * @return boolean
     */
    public function supports(ParamConfiguration $configuration)
    {
        return in_array($configuration->getName(), static::$parameters);
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     */
    public function apply(Request $request, ParamConfiguration $configuration)
    {
        $conversionMethod = $this->inflectConversionMethod($configuration);
        $value = $this->{$conversionMethod}($request);
        $request->attributes->set($configuration->getName(), $value);
    }

    /**
     * @param ParamConfiguration $configuration
     * @return string
     */
    private function inflectConversionMethod(ParamConfiguration $configuration)
    {
        $conversionMethod = 'convert' . ucfirst($configuration->getName());
        assert(method_exists($this, $conversionMethod));
        return $conversionMethod;
    }

    /**
     * @param Request $request
     * @return Book
     * @throws NotFoundHttpException
     */
    private function convertBook(Request $request)
    {
        $uuid = $this->convertId($request);

        try {
            return $this->bookService->getBook($uuid);
        } catch (ObjectNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }

    /**
     * @param Request $request
     * @return Uuid
     * @throws BadRequestHttpException
     */
    private function convertId(Request $request)
    {
        $id = $request->attributes->get('id');

        if (empty($id)) {
            throw new BadRequestHttpException("ID must not be empty.");
        }

        return Uuid::createFromValue($id);
    }

    /**
     * @param Request $request
     * @return integer
     * @throws BadRequestHttpException
     * @throws PreconditionFailedHttpException
     */
    private function convertVersion(Request $request)
    {
        $eTags = $request->getETags();

        if (empty($eTags)) {
            throw new BadRequestHttpException("Expected version header.");
        }

        $eTag = reset($eTags);
        $book = $this->convertBook($request);
        $bookTag = hash('sha256', $book->getId() . $book->getVersion());

        if ($eTag !== $bookTag) {
            throw new PreconditionFailedHttpException("Expected version does not match actual version.");
        }

        return $book->getVersion();
    }
}
