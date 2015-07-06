<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ApiController;
use AppBundle\Domain\Service\BookService;

class ApiControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBookCallsExecuteOnService()
    {
        $service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects(self::atLeastOnce())
            ->method('execute');

        $controller = new ApiController($service);
        $controller->addBookAction('foo', 'bar');
    }
}
