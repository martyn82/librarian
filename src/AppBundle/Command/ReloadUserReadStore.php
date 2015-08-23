<?php

namespace AppBundle\Command;

use AppBundle\Command\ReloadReadStore\User as UserReplay;
use AppBundle\Domain\ReadModel\User as UserDocument;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\Storage;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @DI\Service("commands.reload_read_store.user")
 * @DI\Tag("console.command")
 */
class ReloadUserReadStore extends Command
{
    /**
     * @var EventStore
     */
    private $events;

    /**
     * @var Storage
     */
    private $documents;

    /**
     * @DI\InjectParams({
     *  "events" = @DI\Inject("librarian.eventstore.user"),
     *  "documents" = @DI\Inject("librarian.storage.documents.user")
     * })
     *
     * @param EventStore $events
     * @param Storage $documents
     */
    public function __construct(EventStore $events, Storage $documents)
    {
        $this->events = $events;
        $this->documents = $documents;

        parent::__construct();
    }

    /**
     */
    protected function configure()
    {
        $this->setName('readstore:reload:user')
            ->setDescription('Reload the read store from event store.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Reload read store from event store.");
        $this->documents->clear();

        $output->write("Loading aggregate identities... ");
        $ids = $this->events->getAggregateIds();
        $output->writeln("done.");

        $total = count($ids);
        $current = 0;

        foreach ($ids as $id) {
            /* @var $id Uuid */
            $eventStream = $this->events->getEventsForAggregate($id);

            $aggregate = new UserReplay($id);
            $aggregate->loadFromHistory($eventStream);

            $document = $this->createDocumentFromAggregate($aggregate, $eventStream->size());
            $this->documents->upsert($id, $document);

            $output->write(sprintf("%d/%d\r", ++$current, $total));
        }

        $output->writeln("\nDone.");
    }

    /**
     * @param UserReplay $user
     * @param integer $version
     * @return UserDocument
     */
    private function createDocumentFromAggregate(UserReplay $user, $version)
    {
        return new UserDocument(
            $user->getId(),
            $user->getUserName(),
            $user->getEmailAddress(),
            $user->getFullName(),
            $version
        );
    }
}
