<?php

namespace AppBundle\Command;

use AppBundle\Command\ReloadReadStore\Book as BookReplay;
use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book as BookDocument;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\Storage;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @DI\Service("commands.reload_read_store.book")
 * @DI\Tag("console.command")
 */
class ReloadBookReadStore extends Command
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
     *  "events" = @DI\Inject("librarian.eventstore.book"),
     *  "documents" = @DI\Inject("librarian.storage.documents.book")
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
        $this->setName('readstore:reload:book')
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

            $aggregate = new BookReplay($id);
            $aggregate->loadFromHistory($eventStream);

            $document = $this->createDocumentFromAggregate($aggregate, $eventStream->size());
            $this->documents->upsert($id, $document);

            $output->write(sprintf("%d/%d\r", ++$current, $total));
        }

        $output->writeln("\nDone.");
    }

    /**
     * @param BookReplay $book
     * @param integer $version
     * @return BookDocument
     */
    private function createDocumentFromAggregate(BookReplay $book, $version)
    {
        $authors = array_map(
            function (AuthorAdded $event) {
                return new Author($event->getFirstName(), $event->getLastName());
            },
            $book->getAuthors()
        );

        return new BookDocument(
            $book->getId(),
            new Authors($authors),
            $book->getTitle(),
            $book->getISBN(),
            $book->isAvailable(),
            $version
        );
    }
}
