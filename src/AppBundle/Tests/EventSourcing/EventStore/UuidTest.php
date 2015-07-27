<?php

namespace AppBundle\Tests\EventSourcing\EventStore;

use AppBundle\EventSourcing\EventStore\Uuid;

class UuidTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewWillGenerateAUniqueID()
    {
        $Uuid = Uuid::createNew();

        self::assertNotNull($Uuid->getValue());
        self::assertNotEmpty($Uuid->getValue());
        self::assertEquals($Uuid->getValue(), $Uuid->__toString());
    }

    /**
     * @group uuid-small
     */
    public function testNewUuidGeneratesUniqueStringSmall()
    {
        $this->performUuidGenerationTest(10);
    }

    /**
     * @group uuid-medium
     */
    public function testNewUuidGeneratesUniqueStringMedium()
    {
        $this->performUuidGenerationTest(1000);
    }

    /**
     * @group uuid-big
     */
    public function testNewUuidGeneratesUniqueStringBig()
    {
        $this->performUuidGenerationTest(1000000);
    }

    /**
     * @param integer $cycles
     */
    private function performUuidGenerationTest($cycles)
    {
        $progress = '-';
        $split = 1000;

        $uuids = [];

        for ($i = 0; $i < $cycles; $i++) {
            $uuids[Uuid::createNew()->getValue()] = $i;

            if ($i % $split == 0) {
                echo $progress;
            }

            ob_flush();
        }

        self::assertNotEmpty($uuids);
        self::assertCount($cycles, $uuids);
    }

    public function testSerialization()
    {
        $uuid = Uuid::createNew();
        $serialized = $uuid->serialize();
        $deserialized = Uuid::deserialize($serialized);

        self::assertEquals($uuid->getValue(), $deserialized->getValue());
    }
}
