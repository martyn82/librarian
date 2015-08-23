<?php

namespace AppBundle\Tests\Command;

use AppBundle\Command\ReloadUserReadStore;
use AppBundle\Domain\Message\Event\UserCreated;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Events;
use AppBundle\EventSourcing\ReadStore\Storage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReloadUserReadStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteRetrievesIdentitiesFromEventStoreAndInsertsDocumentsIntoReadStore()
    {
        $eventStore = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readStore = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identities = [
            Uuid::createFromValue(1),
            Uuid::createFromValue(2),
            Uuid::createFromValue(3),
            Uuid::createFromValue(4),
            Uuid::createFromValue(5)
        ];

        $eventStore->expects(self::once())
            ->method('getAggregateIds')
            ->will(self::returnValue($identities));

        $eventStore->expects(self::exactly(count($identities)))
            ->method('getEventsForAggregate')
            ->will(self::returnCallback(
                function (Uuid $id) {
                    return new Events(
                        [
                            new UserCreated($id, 'user', 'email', 'name')
                        ]
                    );
                }
            ));

        $readStore->expects(self::exactly(count($identities)))
            ->method('upsert');

        $in = $this->getMockBuilder(InputInterface::class)
            ->getMockForAbstractClass();

        $out = $this->getMockBuilder(OutputInterface::class)
            ->getMockForAbstractClass();

        $reload = new ReloadUserReadStore($eventStore, $readStore);
        $reload->run($in, $out);
    }
}
