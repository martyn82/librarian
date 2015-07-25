<?php

namespace AppBundle\Tests\Rest;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book as BookReadModel;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use AppBundle\Rest\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class ParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BookService
     */
    private $service;

    /**
     * @var ParamConfiguration
     */
    private $configuration;

    protected function setUp()
    {
        $this->service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configuration = $this->getMockBuilder(ParamConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider paramConverterNameProvider
     * @param string $name
     */
    public function testParamConverterSupports($name)
    {
        $this->configuration->expects(self::any())
            ->method('getName')
            ->will(self::returnValue($name));

        $this->configuration->expects(self::any())
            ->method('getClass')
            ->will(self::returnValue($name));

        $converter = new ParamConverter($this->service);
        self::assertTrue($converter->supports($this->configuration));
    }

    public function paramConverterNameProvider()
    {
        return [
            ['version'],
            [BookReadModel::class],
            [Uuid::class],
            [BookResource::class]
        ];
    }

    public function testApplyConverterOnIdAddsUuid()
    {
        $id = Uuid::createNew();

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id->getValue());

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('id'));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);

        self::assertEquals($id->getValue(), $request->attributes->get('id')->getValue());
    }

    public function testApplyConverterOnIdWithEmptyValueThrowsException()
    {
        self::setExpectedException(BadRequestHttpException::class);

        $request = Request::createFromGlobals();
        $request->attributes->set('id', '');

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('id'));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnBookAddsBook()
    {
        $id = Uuid::createNew();
        $book = new BookReadModel($id, new Authors(), 'title', 'isbn', 1);

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id);

        $this->service->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::returnValue($book));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('book'));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);

        self::assertEquals($book->getId()->getValue(), $request->attributes->get('book')->getId()->getValue());
    }

    public function testApplyConverterOnBookThatCannotBeFoundThrowsException()
    {
        self::setExpectedException(NotFoundHttpException::class);

        $id = Uuid::createNew();

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id);

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('book'));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->service->expects(self::once())
            ->method('getBook')
            ->will(self::throwException(new ObjectNotFoundException('Book', $id)));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnVersionRetrievesVersion()
    {
        $id = Uuid::createNew();
        $version = 1;
        $book = new BookReadModel($id, new Authors(), 'title', 'isbn', $version);

        $request = Request::createFromGlobals();
        $etag = hash('sha256', $id->getValue() . $version);
        $request->headers->set('if-none-match', $etag);
        $request->attributes->set('id', $id);

        $this->service->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::returnValue($book));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('version'));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);

        self::assertEquals($version, $request->attributes->get('version'));
    }

    public function testApplyConverterOnVersionWithoutEtagsThrowsException()
    {
        self::setExpectedException(BadRequestHttpException::class);

        $id = Uuid::createNew();

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id->getValue());

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('version'));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnVersionWithInvalidVersionThrowsException()
    {
        self::setExpectedException(PreconditionFailedHttpException::class);

        $id = Uuid::createNew();
        $version = 1;
        $book = new BookReadModel($id, new Authors(), 'title', 'isbn', $version);

        $request = Request::createFromGlobals();
        $request->headers->set('if-none-match', 'foo');
        $request->attributes->set('id', $id);

        $this->service->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::returnValue($book));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('version'));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $converter = new ParamConverter($this->service);
        $converter->apply($request, $this->configuration);
    }
}