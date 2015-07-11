<?php

namespace AppBundle\EventStore;

class UuidTest extends \PHPUnit_Framework_TestCase
{
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
     * @param int $cycles
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
}
