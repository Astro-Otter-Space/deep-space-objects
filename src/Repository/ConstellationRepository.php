<?php
namespace App\Repository;

use App\Entity\Constellation;
use Elastica\Client;
use Elastica\Document;

/**
 * Class ConstellationRepository
 * @package App\Repository
 */
final class ConstellationRepository extends AbstractRepository
{

    const INDEX_NAME = 'constellations';

    const SEARCH_SIZE = 15;

    /**
     * ConstellationRepository constructor.
     * @param Client $client
     * @param $locale
     */
    public function __construct(Client $client, $locale)
    {
        parent::__construct($client, $locale);
    }

    /**
     * @param $id
     * @return Constellation
     */
    public function getObjectById($id)
    {
        $resultDocument = $this->findById(ucfirst($id));
        if (0 < $resultDocument->getTotalHits()) {
            $dataDocument = $resultDocument->getResults()[0]->getDocument();
            return $this->buildEntityFromDocument($dataDocument);
        } else {
            return null;
        }
    }

    /**
     * Build an entity from result
     * @param $document
     * @return Constellation
     */
    private function buildEntityFromDocument(Document $document)
    {
        /** @var Constellation $constellation */
        $entity = $this->getEntity();
        $constellation = new $entity;

        $constellation = $constellation->setLocale($this->getLocale())->buildObject($document);

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

    /**
     * @return string
     */
    public function getType(): string {
        return self::TYPE_NAME;
    }
}
