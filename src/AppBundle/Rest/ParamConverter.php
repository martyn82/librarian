<?php

namespace AppBundle\Rest;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Domain\ReadModel\Book as BookReadModel;
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
    private static $supportedNames = [
        'version'
    ];

    /**
     * @var string[]
     */
    private static $supportedClasses = [
        BookReadModel::class,
        BookResource::class,
        Uuid::class
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
        return in_array($configuration->getName(), static::$supportedNames)
            || in_array($configuration->getClass(), static::$supportedClasses);
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     */
    public function apply(Request $request, ParamConfiguration $configuration)
    {
        $conversionMethod = $this->inflectConversionMethod($configuration);
        $value = $this->{$conversionMethod}($request, $configuration);
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
     * @param ParamConfiguration $configuration
     * @return Book
     * @throws NotFoundHttpException
     */
    private function convertBook(Request $request, ParamConfiguration $configuration)
    {
        $uuid = $request->attributes->get($configuration->getOptions()['id']);
        return $this->getBook($uuid);
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     * @return Uuid
     * @throws BadRequestHttpException
     */
    private function convertId(Request $request, ParamConfiguration $configuration)
    {
        $id = $request->attributes->get('id');

        if (empty($id)) {
            throw new BadRequestHttpException("ID must not be empty.");
        }

        return Uuid::createFromValue($id);
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     * @return integer
     * @throws BadRequestHttpException
     * @throws PreconditionFailedHttpException
     */
    private function convertVersion(Request $request, ParamConfiguration $configuration)
    {
        $eTags = (array)$request->getETags();
        $eTag = reset($eTags);

        if (empty($eTag)) {
            throw new BadRequestHttpException("Expected version header.");
        }

        $id = $request->attributes->get($configuration->getOptions()['id']);
        $book = $this->getBook($id);

        $bookTag = hash('sha256', $book->getId() . $book->getVersion());

        if ($eTag !== $bookTag) {
            throw new PreconditionFailedHttpException("Expected version does not match actual version.");
        }

        return $book->getVersion();
    }

    /**
     * @param Uuid $id
     * @return BookReadModel
     * @throws NotFoundHttpException
     */
    private function getBook(Uuid $id)
    {
        try {
            return $this->bookService->getBook($id);
        } catch (ObjectNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }
}
