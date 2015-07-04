<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ApiController;
use AppBundle\Domain\Service\BookService;

class ApiControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookCallsExecuteOnService()
    {
        $service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects(self::once())
            ->method('execute');

        $controller = new ApiController($service);
        $controller->registerBookAction('foo');
    }
}
