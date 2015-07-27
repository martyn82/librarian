<?php

namespace AppBundle\Tests\Compare;

use AppBundle\Compare\Comparable;
use AppBundle\Compare\SimpleComparator;

class SimpleComparatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider comparableValuesProvider
     * @param mixed $a
     * @param mixed $b
     * @param boolean $areEqual
     */
    public function testEqualsReturnsTrueIfTwoValuesAreEqual($a, $b, $areEqual)
    {
        $comparator = new SimpleComparator();
        self::assertEquals($areEqual, $comparator->equals($a, $b));
    }

    /**
     * @return array
     */
    public function comparableValuesProvider()
    {
        $obj1a = new \stdClass();
        $obj1b = new \stdClass();

        $obj2a = $this->getMockBuilder(Comparable::class)
            ->getMockForAbstractClass();
        $obj2a->property = 'value';

        $obj2b = $this->getMockBuilder(Comparable::class)
            ->getMockForAbstractClass();
        $obj2b->property = 'value';

        $obj2a->expects(self::any())
            ->method('equals')
            ->with($obj2b)
            ->will(self::returnValue($obj2a->property === $obj2b->property));

        $obj3a = $this->getMockBuilder(Comparable::class)
            ->getMockForAbstractClass();;
        $obj3a->property = 'value';

        $obj3b = $this->getMockBuilder(Comparable::class)
            ->getMockForAbstractClass();
        $obj3b->property = 'val';

        $obj3a->expects(self::any())
            ->method('equals')
            ->with($obj3b)
            ->will(self::returnValue($obj3a->property === $obj3b->property));

        return [
            ['', '', true],
            ['', 0, false],
            [1, 1, true],
            ['1', 1, false],
            [0, null, false],
            [null, null, true],
            [['a', 'b'], ['a', 'b'], true],
            [['a', 'b'], ['b', 'a'], false],
            ['a', 'a', true],
            ['123ab', 123, false],
            [$obj1a, $obj1b, false],
            [$obj2a, $obj2b, true],
            [$obj3a, $obj3b, false],
            [true, true, true],
            [true, false, false]
        ];
    }
}
