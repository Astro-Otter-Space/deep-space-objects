<?php


namespace App\Repository;

use App\Entity\ES\Event;
use App\Entity\ES\ListObservations;
use Elastica\Document;
use Elastica\Query;
use Elastica\ResultSet;
use Elastica\Search;

/**
 * Class EventRepository
 * @package App\Repository
 */
class EventRepository extends AbstractRepository
{
    const INDEX_NAME = 'events';

    /**
     * @param $id
     *
     * @return Event|null
     * @throws \ReflectionException
     */
    public function getEventById($id):? Event
    {
        /** @var ResultSet $observationDoc */
        $document = $this->findById($id);

        if (0 < $document->getTotalHits()) {
            $eventDoc = $document->getResults()[0]->getDocument();

            return $this->buildEntityFromDocument($eventDoc);
        } else {
            return null;
        }
    }


    /**
     *
     */
    public function getEventBySearchTerms()
    {

    }

    /**
     * Get all futur events
     * @throws \ReflectionException
     */
    public function getAllFuturEvents(): ListObservations
    {
        /** @var \DateTimeInterface $now */
        $now = new \DateTime();

        /** @var ListObservations $listObservation */
        $listObservation = new ListObservations();

        /** @var Query $query */
        $query = new Query();

        /** @var Query\Range $mustQuery */
        $rangeQuery = new Query\Range();
        $rangeQuery->addField('event_date', [
            'gte' => $now->format('Y-m-d')
        ]);

        /** @var Query\BoolQuery $boolQuery */
        $boolQuery = new Query\BoolQuery();
        $boolQuery->addMust($rangeQuery);

        /** @var Search $search */
        $search = new Search($this->client);

        /** @var ResultSet $result */
        $result = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $result->count()) {
            foreach ($result->getDocuments() as $document) {
                $event = $this->buildEntityFromDocument($document);
                $listObservation->addEvent($event);
            }
        }

        return $listObservation;
    }

    public function addEvent()
    {

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

        return $event->setLocale()->buildObjectR($document);
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'App\Entity\ES\Event';
    }


    /**
     * @return string
     */
    protected function getType()
    {
        return self::INDEX_NAME;
    }

}
