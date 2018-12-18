<?php
namespace App\Repository;

use App\Entity\Constellation;

/**
 * Class ConstellationRepository
 * @package App\Repository
 */
final class ConstellationRepository extends AbstractRepository
{

    const INDEX_NAME = 'constellations';

    const SEARCH_SIZE = 15;

    /**
     * @param $id
     * @return Constellation
     */
    public function getObjectById($id)
    {
        $document = $this->findById(ucfirst($id));
//        if (0 < $document->count()) {
//            return $this->buildEntityFromDocument($document->getDocuments());
//        } else {
//            return null;
//        }
        return null;
    }


    /**
     *
     */
    public function getList()
    {

    }

    /**
     * Build an entityfrom result
     * @param $document
     * @return Constellation
     */
    private function buildEntityFromDocument($document)
    {
        /** @var Constellation $entity */
        $entity = $this->getEntity();
        $constellation = $entity->setLocale($this->getLocale())->buildObject($document);

        // Todo : add gerateurlhelper;

        return $constellation;
    }

    /**
     * @return Constellation
     */
    public function getEntity(): string
    {
        return 'App\Entity\Constellation';
    }
}
