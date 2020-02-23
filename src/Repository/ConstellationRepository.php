<?php
namespace App\Repository;

use App\Entity\ES\Constellation;
use App\Entity\ES\ListConstellation;
use Elastica\Document;
use Elastica\Query;
use Elastica\Result;
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

    private static $listSearchFields = [
        'id',
        'id.raw',
        'data.gen',
        'data.alt.alt'
    ];

    /**
     * @param $id
     * @return Constellation
     * @throws \ReflectionException
     */
    public function getObjectById($id):? Constellation
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
    public function getAllConstellation(): \Generator
    {
        /** @var ListConstellation $listConstellation */
        //$listConstellation = new ListConstellation();
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
                yield $this->buildEntityFromDocument($document);
                //$listConstellation->addConstellation($constellation);
            }
        }

        //return $listConstellation;
    }

    /**
     * Search autocomplete
     *
     * @param $searchTerm
     * @return array
     */
    public function getConstellationsBySearchTerms($searchTerm): array
    {
        $list = [];

        if ('en' !== $this->getLocale()) {
            array_push(self::$listSearchFields, sprintf('data.alt.alt_%s', $this->getLocale()));
            array_push(self::$listSearchFields, sprintf('data.alt.alt_%s.keyword', $this->getLocale()));
        }

        $result = $this->requestBySearchTerms($searchTerm, self::$listSearchFields);
        if (0 < $result->getTotalHits()) {
            $list = array_map(function(Result $doc) {
                return $this->buildEntityFromDocument($doc->getDocument());
            }, $result->getResults());
        }

        return $list;
    }

    /**
     * Build an entity from result
     * @param Document $document
     * @return Constellation
     * @throws \ReflectionException
     */
    private function buildEntityFromDocument(Document $document): Constellation
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
        return 'App\Entity\ES\Constellation';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::INDEX_NAME;
    }
}
