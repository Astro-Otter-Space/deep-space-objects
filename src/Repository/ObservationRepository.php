<?php


namespace App\Repository;

use App\Entity\Observation;
use Elastica\ResultSet;

final class ObservationRepository extends AbstractRepository
{
    const INDEX_NAME = 'observations';


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
            return $this->buildEntityFromDocument($document);
        } else {
            return null;
        }
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