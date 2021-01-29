<?php


namespace App\Repository;

use App\Classes\Utils;
use App\Entity\ES\Event;
use App\Entity\ES\ListEvents;
use App\Entity\ES\ListObservations;
use Elastica\Document;
use Elastica\Query;
use Elastica\Response;
use Elastica\Result;
use Elastica\ResultSet;
use Elastica\Search;

/**
 * @deprecated
 * Class EventRepository
 * @package App\Repository
 */
class EventRepository //extends AbstractRepository
{
    public const INDEX_NAME = 'events';

    private static array $listSearchFields = ['name', 'description', 'locationLabel', 'organiserName'];

    /**
     * @param $id
     *
     * @return Event|null
     * @throws \ReflectionException
     */
    public function getEventById($id): ?Event
    {
        /** @var ResultSet $observationDoc */
        $document = $this->findById($id);

        if (0 < $document->getTotalHits()) {
            $eventDoc = $document->getResults()[0]->getDocument();

            return $this->buildEntityFromDocument($eventDoc);
        }

        return null;
    }


    /**
     * @param $terms
     *
     * @return \Generator
     * @throws \ReflectionException
     */
    public function getEventBySearchTerms($terms): \Generator
    {
        $list = [];
        /** @var ResultSet $result */
        $result = $this->requestBySearchTerms($terms, self::$listSearchFields);

        if (0 < $result->getTotalHits()) {

            foreach ($result->getResults() as $doc) {
                yield $this->buildEntityFromDocument($doc->getDocument());
            }
        }
    }

    /**
     * Get all futur events
     * @throws \ReflectionException
     */
    public function getAllFuturEvents(): \Generator
    {
        /** @var Query $query */
        $query = new Query();

        /** @var Query\Range $mustQuery */
        $rangeQuery = new Query\Range();
        $rangeQuery->addField('event_date', [
            'gte' => 'now'
        ]);

        /** @var Query\BoolQuery $boolQuery */
        $boolQuery = new Query\BoolQuery();
        $boolQuery->addMust($rangeQuery);

        $query->setQuery($boolQuery)->setFrom(0)->setSize(500);

        /** @var Search $search */
        $search = new Search($this->client);

        /** @var ResultSet $result */
        $result = $search->addIndex(self::INDEX_NAME)->search($query);
        if (0 < $result->count()) {
            foreach ($result->getDocuments() as $document) {
                yield $this->buildEntityFromDocument($document);
            }
        }
    }


    /**
     * Add normalized data into elasticsearch document
     * @param $id
     * @param $eventData
     *
     * @return Response
     */
    public function addEvent($id, $eventData): Response
    {
        /** @var Document $document */
        $document = new Document($id, $eventData, '', self::INDEX_NAME);

        return $this->addNewDocument($document);
    }

    /**
     * @param Document $document
     *
     * @return Event
     * @throws \ReflectionException
     */
    private function buildEntityFromDocument(Document $document): Event
    {
        $entity = $this->getEntity();

        /** @var Event $event */
        $event = new $entity;

        return $event->setLocale($this->locale)->buildObjectR($document);
    }

    /**
     * @return string
     */
    protected function getEntity(): string
    {
        return Event::class;
    }

    /**
     * @return string
     */
    protected function getIndex(): string
    {
        return self::INDEX_NAME;
    }
}
