<?php
namespace App\Repository;

use App\Entity\Constellation;
use Elastica\Client;

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
        $document = $this->findById(ucfirst($id));
        if (0 < $document->getTotalHits()) {
            $dataDocument = $document->getResults()[0]->getDocument()->getData();
            return $this->buildEntityFromDocument($dataDocument);
        } else {
            return null;
        }
    }

    /**
     * Build an entityfrom result
     * @param $document
     * @return Constellation
     */
    private function buildEntityFromDocument($document)
    {
        /** @var Constellation $entity */
        $constellation = $this->getEntity();
        dump($this->getLocale());
//        $locale = $this->getLocale();
        $constellation = $constellation->buildObject($document);

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
