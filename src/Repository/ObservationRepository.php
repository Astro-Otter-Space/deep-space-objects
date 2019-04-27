<?php


namespace App\Repository;

use App\Entity\Observation;
use Elastica\Result;
use Elastica\ResultSet;

/**
 * Class ObservationRepository
 *
 * @package App\Repository
 */
final class ObservationRepository extends AbstractRepository
{
    const INDEX_NAME = 'observations';

    private static $listSearchFields = [
        'id',
        'username',
        'location_label'
    ];

    /**
     * @param $id
     *
     * @return Observation|null
     * @throws \ReflectionException
     */
    public function getObservationById($id): Observation
    {
        /** @var ResultSet $observationDoc */
        $document = $this->findById($id);

        if (0 < $document->getTotalHits()) {
            $observationDoc = $document->getResults()[0]->getDocument();

            return $this->buildEntityFromDocument($observationDoc);
        } else {
            return null;
        }
    }


    /**
     * Build list of Observation from search term
     * @param $terms
     * @return array
     */
    public function getObservationsBySearchTerms($terms)
    {
        $list = [];
        /** @var ResultSet $result */
        $result = $this->requestBySearchTerms($terms, self::$listSearchFields);

        if (0 < $result->getTotalHits()) {
            $list = array_map(function(Result $doc) {
                return $this->buildEntityFromDocument($doc->getDocument());
            }, $result->getResults());
        }
        return $list;
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'App\Entity\Observation';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::INDEX_NAME;
    }


    /**
     * @param $document
     *
     * @return Observation
     * @throws \ReflectionException
     */
    private function buildEntityFromDocument($document)
    {
        $entity = $this->getEntity();
        /** @var Observation $observation */
        $observation = new $entity;

        $observation->setLocale($this->getLocale())->buildObjectR($document);

        return $observation;
    }
}