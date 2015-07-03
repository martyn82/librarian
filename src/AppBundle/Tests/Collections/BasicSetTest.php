<?php
namespace Collections;

use AppBundle\Collections\BasicCollection;
use AppBundle\Collections\BasicSet;

class AbstractSetTest extends \PHPUnit_Framework_TestCase
{
    public function testAddIsIdempotent()
    {
        $set = new BasicSet();
        $result = $set->add('foo');
        self::assertTrue($result);

        $result = $set->add('foo');
        self::assertFalse($result);

        self::assertEquals(1, $set->size());
    }

    public function testAddAllAddsUnique()
    {
        $setA = new BasicSet();
        $setA->add('foo');

        $collectionB = new BasicCollection();
        $collectionB->add('foo');

        self::assertFalse($setA->addAll($collectionB));
    }

    public function testRemoveAllYieldsAsymmetricDifference()
    {
        $setA = new BasicSet();
        $setA->add('foo');
        $setA->add('bar');

        $setB = new BasicSet();
        $setB->add('foo');
        $setB->add('baz');

        $result = $setA->removeAll($setB);

        self::assertTrue($result);
        self::assertTrue($setA->contains('bar'));
        self::assertFalse($setA->contains('foo'));
    }

    public function testAddAllYieldsUnion()
    {
        $setA = new BasicSet();
        $setA->add('foo');
        $setA->add('bar');

        $setB = new BasicSet();
        $setB->add('foo');
        $setB->add('baz');

        $setA->addAll($setB);

        self::assertTrue($setA->containsAll($setB));
    }
}
