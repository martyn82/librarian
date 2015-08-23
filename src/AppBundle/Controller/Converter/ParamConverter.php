<?php

namespace AppBundle\Controller\Converter;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\User as UserResource;
use AppBundle\Domain\ReadModel\Book as BookReadModel;
use AppBundle\Domain\ReadModel\User as UserReadModel;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\Domain\Service\UserService;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\ReadModel;
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
        Uuid::class,
        BookResource::class,
        UserResource::class
    ];

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var BookService
     */
    private $bookService;

    /**
     * @param BookService $bookService
     * @param UserService $userService
     */
    public function __construct(BookService $bookService, UserService $userService)
    {
        $this->bookService = $bookService;
        $this->userService = $userService;
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
        if ($configuration->getName() == 'version') {
            $name = 'version';
        } else {
            $parts = explode('\\', $configuration->getClass());
            $name = end($parts);
        }

        $conversionMethod = 'convert' . ucfirst($name);
        assert(method_exists($this, $conversionMethod), $conversionMethod);
        return $conversionMethod;
    }

    /**
     * @param ParamConfiguration $configuration
     * @return string
     */
    private function inflectRetrieverMethod(ParamConfiguration $configuration)
    {
        $parts = explode('\\', $configuration->getClass());
        $retrieverMethod = 'get' . ucfirst(end($parts));
        assert(method_exists($this, $retrieverMethod), $retrieverMethod);
        return $retrieverMethod;
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     * @return Uuid
     * @throws BadRequestHttpException
     */
    protected function convertUuid(Request $request, ParamConfiguration $configuration)
    {
        $uuid = $request->attributes->get($configuration->getName());

        if (empty($uuid)) {
            throw new BadRequestHttpException("Uuid value of '{$configuration->getName()}' must not be empty.");
        }

        return Uuid::createFromValue($uuid);
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     * @return UserReadModel
     * @throws NotFoundHttpException
     */
    protected function convertUser(Request $request, ParamConfiguration $configuration)
    {
        $uuid = $request->attributes->get($configuration->getOptions()['id']);
        return $this->getUser($uuid);
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     * @return integer
     * @throws BadRequestHttpException
     * @throws PreconditionFailedHttpException
     */
    protected function convertVersion(Request $request, ParamConfiguration $configuration)
    {
        $eTags = (array)$request->getETags();
        $eTag = reset($eTags);

        if (empty($eTag)) {
            throw new BadRequestHttpException("Expected version header.");
        }

        $id = $request->attributes->get($configuration->getOptions()['id']);
        $retrieverMethod = $this->inflectRetrieverMethod($configuration);
        $entity = $this->{$retrieverMethod}($id);

        $tag = hash('sha256', $entity->getId() . $entity->getVersion());

        if ($eTag !== $tag) {
            throw new PreconditionFailedHttpException("Expected version does not match actual version.");
        }

        return $entity->getVersion();
    }

    /**
     * @param Request $request
     * @param ParamConfiguration $configuration
     * @return BookReadModel
     * @throws NotFoundHttpException
     */
    protected function convertBook(Request $request, ParamConfiguration $configuration)
    {
        $uuid = $request->attributes->get($configuration->getOptions()['id']);
        return $this->getBook($uuid);
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

    /**
     * @param Uuid $id
     * @return UserReadModel
     * @throws NotFoundHttpException
     */
    private function getUser(Uuid $id)
    {
        try {
            return $this->userService->getUser($id);
        } catch (ObjectNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }
    }
}
