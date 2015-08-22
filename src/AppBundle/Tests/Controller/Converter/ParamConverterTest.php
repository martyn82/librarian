<?php

namespace AppBundle\Tests\Controller\Converter;

use AppBundle\Controller\Converter\ParamConverter;
use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\User as UserResource;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book as BookReadModel;
use AppBundle\Domain\ReadModel\User as UserReadModel;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\Domain\Service\UserService;
use AppBundle\EventSourcing\EventStore\Uuid;
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
    private $bookService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ParamConfiguration
     */
    private $configuration;

    protected function setUp()
    {
        $this->bookService = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userService = $this->getMockBuilder(UserService::class)
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

        $converter = new ParamConverter($this->bookService, $this->userService);
        self::assertTrue($converter->supports($this->configuration));
    }

    public function paramConverterNameProvider()
    {
        return [
            ['version'],
            [BookReadModel::class],
            [Uuid::class],
            [BookResource::class],
            [UserReadModel::class],
            [UserResource::class]
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

        $converter = new ParamConverter($this->bookService, $this->userService);
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

        $converter = new ParamConverter($this->bookService, $this->userService);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnBookAddsBook()
    {
        $id = Uuid::createNew();
        $book = new BookReadModel($id, new Authors(), 'title', 'isbn', true, 1);

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id);

        $this->bookService->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::returnValue($book));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('book'));

        $converter = new ParamConverter($this->bookService, $this->userService);
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

        $this->bookService->expects(self::once())
            ->method('getBook')
            ->will(self::throwException(new ObjectNotFoundException('Book', $id)));

        $converter = new ParamConverter($this->bookService, $this->userService);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnUserThatCannotBeFoundThrowsException()
    {
        self::setExpectedException(NotFoundHttpException::class);

        $id = Uuid::createNew();

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id);

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('user'));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->userService->expects(self::once())
            ->method('getUser')
            ->will(self::throwException(new ObjectNotFoundException('User', $id)));

        $converter = new ParamConverter($this->bookService, $this->userService);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnVersionRetrievesVersion()
    {
        $id = Uuid::createNew();
        $version = 1;
        $book = new BookReadModel($id, new Authors(), 'title', 'isbn', true, $version);

        $request = Request::createFromGlobals();
        $etag = hash('sha256', $id->getValue() . $version);
        $request->headers->set('if-none-match', $etag);
        $request->attributes->set('id', $id);

        $this->bookService->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::returnValue($book));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('version'));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->configuration->expects(self::once())
            ->method('getClass')
            ->will(self::returnValue(BookReadModel::class));

        $converter = new ParamConverter($this->bookService, $this->userService);
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

        $converter = new ParamConverter($this->bookService, $this->userService);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnVersionWithInvalidVersionThrowsException()
    {
        self::setExpectedException(PreconditionFailedHttpException::class);

        $id = Uuid::createNew();
        $version = 1;
        $book = new BookReadModel($id, new Authors(), 'title', 'isbn', true, $version);

        $request = Request::createFromGlobals();
        $request->headers->set('if-none-match', 'foo');
        $request->attributes->set('id', $id);

        $this->bookService->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::returnValue($book));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('version'));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->configuration->expects(self::once())
            ->method('getClass')
            ->will(self::returnValue(BookReadModel::class));

        $converter = new ParamConverter($this->bookService, $this->userService);
        $converter->apply($request, $this->configuration);
    }

    public function testApplyConverterOnUserCreatesUser()
    {
        $id = Uuid::createNew();
        $user = new UserReadModel($id, 'user', 'email', 1);

        $request = Request::createFromGlobals();
        $request->attributes->set('id', $id);

        $this->userService->expects(self::once())
            ->method('getUser')
            ->with($id)
            ->will(self::returnValue($user));

        $this->configuration->expects(self::once())
            ->method('getOptions')
            ->will(self::returnValue(['id' => 'id']));

        $this->configuration->expects(self::atLeastOnce())
            ->method('getName')
            ->will(self::returnValue('user'));

        $converter = new ParamConverter($this->bookService, $this->userService);
        $converter->apply($request, $this->configuration);

        self::assertEquals($user->getId()->getValue(), $request->attributes->get('user')->getId()->getValue());
    }
}