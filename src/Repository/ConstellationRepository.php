<?php
namespace App\Repository;

use App\Entity\Constellation;
use App\Entity\ListConstellation;
use Elastica\Client;
use Elastica\Document;
use Elastica\Query;
use Elastica\Query\MatchAll;
use Elastica\Search;

/**
 * Class ConstellationRepository
 * @package App\Repository
 */
final class ConstellationRepository extends AbstractRepository
{

    const INDEX_NAME = 'constellations';

    const SEARCH_SIZE = 15;

    const URL_MAP = '/build/images/const_maps/%s.gif';

    const URL_IMG = '/build/images/const_thumbs/%s.jpg';

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
     * @throws \ReflectionException
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
     * Build a list of all constellation (88)
     * @throws \ReflectionException
     */
    public function getAllConstellation()
    {
        /** @var ListConstellation $listConstellation */
        $listConstellation = new ListConstellation();
        $this->client->getIndex(self::INDEX_NAME);

        /** @var Query $query */
        $query = new Query();
        $query->setSize(100);
        $query->addSort(['data.order' => ['order' => 'asc']]);

        /** @var Search $search */
        $search = new Search($this->client);
        $result = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $result->count()) {
            foreach ($result->getDocuments() as $document) {
                $constellation = $this->buildEntityFromDocument($document);
                $listConstellation->addConstellation($constellation);
            }
        }

        return $listConstellation;
    }

    public function getConstellationBySearchTerms()
    {

    }

    /**
     * Build an entity from result
     * @param Document $document
     * @return Constellation
     * @throws \ReflectionException
     */
    private function buildEntityFromDocument(Document $document)
    {
        /** @var Constellation $constellation */
        $entity = $this->getEntity();
        $constellation = new $entity;

        $constellation = $constellation->setLocale($this->getLocale())
            ->buildObjectR($document);

        $constellation->setMap(sprintf(self::URL_MAP, strtoupper($constellation->getId())));
        $constellation->setImage(sprintf(self::URL_IMG, strtolower($constellation->getId())));

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
