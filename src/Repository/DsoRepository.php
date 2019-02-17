<?php

namespace App\Repository;

use App\Entity\Dso;
use App\Entity\ListDso;
use App\Managers\DsoManager;
use Elastica\Aggregation\Avg;
use Elastica\Aggregation\Terms;
use Elastica\Client;
use Elastica\Document;
use Elastica\Query;
use Elastica\Result;
use Elastica\Search;
use Elastica\Suggest;

/**
 * Class DsoRepository
 * @package App\Repository
 */
class DsoRepository extends AbstractRepository
{
    private static $listSearchFields = [
        'id',
        'id.raw',
        'data.desigs',
        'data.alt.alt'
    ];

    const INDEX_NAME = 'deepspaceobjects';


    /**
     * Retrieve object by his Id
     *
     * @param $id
     * @return Dso|null
     * @throws \ReflectionException
     */
    public function getObjectById($id)
    {
        $resultDocument = $this->findById(ucfirst($id));
        if (0 < $resultDocument->getTotalHits()) {
            return $this->buildEntityFromDocument($resultDocument->getDocuments()[0]);
        } else {
            return null;
        }
    }

    /**
     * Retrieve  list of Dso objects in a constellation
     * @param $constId
     * @param null $excludedId
     * @param $limit
     * @return ListDso
     * @throws \ReflectionException
     */
    public function getObjectsByConstId($constId, $excludedId = null, $limit): ListDso
    {
        if (empty($limit)) {
            $limit = parent::SIZE;
        }
        /** @var ListDso $dsoList */
        $dsoList = new ListDso();
        $this->client->getIndex(self::INDEX_NAME);

        /** @var Query $query */
        $query = new Query();

        /** @var Query\Term $mustQuery */
        $mustQuery = new Query\Term();
        $mustQuery->setTerm('data.const_id', strtolower($constId));

        $mustNotQuery = new Query\Term();
        $mustNotQuery->setTerm('id', strtolower($excludedId));

        /** @var Query\BoolQuery $boolQuery */
        $boolQuery = new Query\BoolQuery();
        $boolQuery->addMust($mustQuery)
            ->addMustNot($mustNotQuery);

        $query->setQuery($boolQuery);
        $query->setFrom(parent::FROM)->setSize($limit);

        $search = new Search($this->client);
        $search = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $search->count()) {
            foreach ($search->getDocuments() as $document) {
                $dsoList->addDso($this->buildEntityFromDocument($document));
            }
        }

        return $dsoList;
    }


    /**
     * Search autocomplete
     *
     * @param $searchTerm
     * @return array
     */
    public function getObjectsBySearchTerms($searchTerm)
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
     * Catalog Research, with|without filters
     * Get aggregates
     *
     * @param $from
     * @param $size
     * @param $filters
     */
    public function getObjectsCatalogByFilters($from, $filters)
    {
        $this->client->getIndex(self::INDEX_NAME);

        /** @var Query $query */
        $query = new Query();

        if (0 < count($filters)) {
            /** @var Query\BoolQuery $query */
            $boolQuery = new Query\BoolQuery();

            $query->setQuery($boolQuery);
        }

        $query->setFrom($from)->setSize(parent::SIZE);

        // Aggregates
        $listAggregations = [
            'type' => [
                'field' => 'data.type.keyword',
                'size' => 100
            ],
            'catalog' => [
                'field' => 'catalog.keyword',
                'size' => 100
            ],
            'constellation' => [
                'field' => 'data.const_id.keyword',
                'size' => 100
            ]
        ];

        array_walk($listAggregations, function($tab, $type) use($query) {
            /** @var Terms $aggregation */
           $aggregation = new Terms($type);
           $aggregation->setField($tab['field']);
           $aggregation->setSize($tab['size']);

           $query->addAggregation($aggregation);
        });


        /** @var Search $search */
        $search = new Search($this->client);
        $search = $search->addIndex(self::INDEX_NAME)->search($query);

        foreach ($search->getDocuments() as $doc) {
            dump($doc);
        }

        dump($search->getAggregations());
        foreach ($search->getAggregations() as $aggregations) {

        }

        dump(json_encode($search->getQuery()->toArray()));
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'App\Entity\Dso';
    }

    /**
     * Build a Dso entity from document ElasticSearch
     * @param Document $document
     * @return Dso
     * @throws \ReflectionException
     */
    private function buildEntityFromDocument(Document $document)
    {
        $entity = $this->getEntity();
        /** @var Dso $dso */
        $dso = new $entity;

        return $dso->setLocale($this->getLocale())->buildObjectR($document);
    }

    /**
     * @return string
     */
    public function getType(): string {
        return self::INDEX_NAME;
    }

    /**
    $aggregates = [
        'aggregates' => [
            'type' => [
                'terms' => [
                    'field' => 'data.type.keyword',
                    'size' => 20
                ]
            ],
            'const_id' => [
                'terms' => [
                'field' => 'data.const_id.keyword',
                'size' => 100
                ]
            ],
            'mag' => [
                'range' => [
                    'field' => 'data.mag',
                    'ranges' => [
                        ['to' => 5],
                        ['from' => 5, 'to' => 10],
                        ['from' => 10]
                    ]
                ]
            ]
        ],
        'filter' => [
            'term' => [
                'catalog' => $typeCatalog
            ]
        ]
    ];   
**/

}


