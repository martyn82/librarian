<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BooksController;
use AppBundle\Domain\Service\BookService;
use AppBundle\MessageBus\CommandBus;

class BooksControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexActionRetrievesAllBooks()
    {
        $service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects(self::once())
            ->method('getAll');

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new BooksController($service, $commandBus);
        $controller->indexAction();
    }
}
