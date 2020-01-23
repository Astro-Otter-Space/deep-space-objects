<?php

namespace App\Repository;

use App\Classes\Utils;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use Elastica\Aggregation\Range;
use Elastica\Aggregation\Terms;
use Elastica\Document;
use Elastica\Query;
use Elastica\Result;
use Elastica\Search;

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
        'data.alt.alt',
        'data.discover'
    ];

    private static $listAggregates = [
        'constellation' => [
            'field' => 'data.const_id.keyword',
            'size' => 100
        ],
        'catalog' => [
            'field' => 'catalog.keyword',
            'size' => 100
        ],
        'type' => [
            'field' => 'data.type.keyword',
            'size' => 100
        ]
    ];

    private static $listAggregatesRange = [
        'magnitude' => [
            'field' => 'data.mag',
            'ranges' => [
                ['to' => 5, 'key' => 'low'],
                ['from' => 5, 'to' => 10, 'key' => 'average'],
                ['from' => 10, 'to' => 15, 'key' => 'high'],
                ['from' => 15, 'key' => 'hard']
            ]
        ]
    ];

    const INDEX_NAME = 'deepspaceobjects';

    const ASTROBIN_FIELD = 'data.astrobin_id';

    /**
     * Get aggregates proprieties
     * @param bool $onlyKeys
     *
     * @return array
     */
    public function getListAggregates($onlyKeys = false)
    {
        if ($onlyKeys) {
            return array_merge(array_keys(self::$listAggregates), array_keys(self::$listAggregatesRange));
        } else {
            return self::$listAggregates;
        }
    }

    /**
     * Retrieve object by his Id
     *
     * @param $id
     * @param boolean $hydrate
     * @return Dso|Document|null
     * @throws \ReflectionException
     */
    public function getObjectById($id, $hydrate = true)
    {
        $resultDocument = $this->findById(ucfirst($id));
        if (0 < $resultDocument->getTotalHits()) {
            if ($hydrate) {
                return $this->buildEntityFromDocument($resultDocument->getDocuments()[0]);
            } else {
                return $resultDocument->getDocuments()[0];
            }
        } else {
            return null;
        }
    }

    /**
     * Retrieve  list of Dso objects in a constellation
     * @param $constId
     * @param null $excludedId
     * @param int $offset
     * @param int $limit
     * @param bool $hydrate
     * @return ListDso|array
     * @throws \ReflectionException
     */
    public function getObjectsByConstId($constId, $excludedId = null, $offset, $limit, $hydrate = true)
    {
        if (empty($offset) || is_null($offset)) {
            $offset = parent::FROM;
        }

        if (empty($limit) || is_null($limit)) {
            $limit = (int)parent::SIZE;
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

        $query->setFrom($offset)->setSize($limit);

        $query->addSort(
            [
                'data.mag' => ['order' => parent::SORT_ASC, 'mode' => 'avg'],
            ]

        );

        /** @var Search $search */
        $search = new Search($this->client);
        $search = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $search->count()) {
            if (false === $hydrate) {
                return $search->getDocuments();
            }
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
    public function getObjectsBySearchTerms($searchTerm): array
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
     * @param $filters
     * @param int|null $to
     * @param bool $hydrate
     * @return array
     * @throws \ReflectionException
     */
    public function getObjectsCatalogByFilters($from = 0, $filters = [], $to = null, $hydrate = true): array
    {
        $this->client->getIndex(self::INDEX_NAME);
        $size = (is_null($to)) ? parent::SIZE : $to;
        $nbItems = 0;

        /** @var Query $query */
        $query = new Query();

        // BUILD FILTERS
        if (0 < count($filters)) {
            /** @var Query\BoolQuery $query */
            $boolQuery = new Query\BoolQuery();

            // Add filters
            foreach ($filters as $type => $val) {
                /** @var Query\Term $mustQuery */
                $mustQuery = new Query\Term();

                /** @var Query\Range $rangeQuery */
                $rangeQuery = new Query\Range();

                $field = ('magnitude' === $type) ? self::$listAggregatesRange[$type]['field'] : self::$listAggregates[$type]['field'];

                if ('magnitude' === $type) {
                    $keyRange = array_search($val , array_column(self::$listAggregatesRange[$type]['ranges'], 'key'));
                    $range = self::$listAggregatesRange[$type]['ranges'][$keyRange];

                    if (array_key_exists('to', $range)) $paramRange['lte'] =  $range['to'];
                    if (array_key_exists('from', $range)) $paramRange['gte'] =  $range['from'];

                    $rangeQuery->addField($field, $paramRange);

                    $boolQuery->addMust($rangeQuery);
                } else {
                    // truc à la con, à modifer ds les données sources
                    $val = ("constellation" === $type && $val !== Utils::UNASSIGNED) ? ucfirst($val): $val;
                    $mustQuery->setTerm($field, $val);
                    $boolQuery->addMust($mustQuery);
                }
            }

            $query->setQuery($boolQuery);
        }

        // From and size
        $query->setFrom($from)->setSize($size);

        // Sort
        $query->addSort([
            'order' => [
                'order' => parent::SORT_ASC
            ]
        ]);

        // Aggregates
        array_walk(self::$listAggregates, function($tab, $type) use($query) {
            /** @var Terms $aggregation */
            $aggregation = new Terms($type);
            $aggregation->setField($tab['field']);
            $aggregation->setSize($tab['size']);

            $query->addAggregation($aggregation);
        });

        // Aggregates range
        array_walk(self::$listAggregatesRange, function ($tab, $type) use ($query){
            /** @var Range $aggregateRange */
            $aggregationRange = new Range($type);
            $aggregationRange->setField($tab['field']);
            foreach ($tab['ranges'] as $range) {
                $from = $range['from'] ?? null;
                $to = $range['to'] ?? null;
                $key = $range['key'] ?? null;
                $aggregationRange->addRange($from, $to, $key);
            }

            $query->addAggregation($aggregationRange);
        });


        /** @var Search $search */
        $search = new Search($this->client);
        $search = $search->addIndex(self::INDEX_NAME)->search($query);
        $nbItems = $search->getTotalHits();

        if (false === $hydrate) {
            return [$search->getDocuments(), $nbItems];
        }

        /** @var ListDso $listDso */
        $listDso = new ListDso();
        foreach ($search->getDocuments() as $doc) {
            $listDso->addDso($this->buildEntityFromDocument($doc));
        }

        $listAggregations = [];
        foreach ($search->getAggregations() as $type=>$aggregations) {
            $listAggregations[$type] = array_map(function($item) {
                return [$item['key'] => $item['doc_count']];
            }, $aggregations['buckets']);
        }

        $listSort = $this->getListAggregates(true);
        uksort($listAggregations, function ($k1, $k2) use ($listSort) {
            return ((array_search($k1, $listSort) > array_search($k2, $listSort)) ? 1 : -1);
        });

        return [$listDso, $listAggregations, $nbItems];
    }

    /**
     * Retrieve last updated Dso
     * @param \DateTimeInterface $lastUpdate
     *
     * @return ListDso
     * @throws \Exception
     */
    public function getObjectsUpdatedAfter(\DateTimeInterface $lastUpdate): ListDso
    {
        /** @var ListDso $dsoList */
        $dsoList = new ListDso();

        $this->client->getIndex(self::INDEX_NAME);

        $now = new \DateTime('now');

        /** @var Query $query */
        $query = new Query();

        /** @var Query\BoolQuery $boolQuery */
        $boolQuery = new Query\BoolQuery();

        /** @var Range $range */
        $rangeQuery = new Query\Range();
        $rangeQuery->addField('', [
            'gte' => $lastUpdate->format(Utils::FORMAT_DATE_ES),
            'lt' => $now->format(Utils::FORMAT_DATE_ES)
        ]);

        $boolQuery->addMust($rangeQuery);
        $query->setQuery($boolQuery);

        $query->setFrom(0)->setSize(self::MAX_SIZE);

        /** @var Search $search */
        $search = new Search($this->client);
        $search = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $search->count()) {
            /**
             * @param $listDocuments
             *
             * @return \Generator
             */
            $listDsoGenerator = function($listDocuments) {
                foreach ($listDocuments as $document) {
                    yield $this->buildEntityFromDocument($document);
                }
            };

            $listDsoIterator = $listDsoGenerator($search->getDocuments());
            while($listDsoIterator->valid()) {
                /** @var Dso $dso */
                $dso = $listDsoIterator->current();

                $dsoList->addDso($dso);
                $listDsoIterator->next();
            }
        }

        return $dsoList;
    }

    /**
     * Get list of AstrobinId
     *
     * @param array|null $listExcludedAstrobinId
     * @return array
     *
     * Query :
     * {
     *    "query": {
     *      "bool": {
     *        "must": {
     *          "exists": {
     *            "field": "data.astrobin_id"
     *          }
     *        }
     *      }
     *   }
     * }
     */
    public function getAstrobinId(?array $listExcludedAstrobinId): array
    {
        $listAstrobinId = [];

        /** @var Query $query */
        $query = new Query();

        /** @var Query\BoolQuery $boolQuery */
        $boolQuery = new Query\BoolQuery();

        /** @var Query\Exists $mustQuery */
        $mustQuery = new Query\Exists(self::ASTROBIN_FIELD);
        $boolQuery->addMust($mustQuery);

        if (!is_null($listAstrobinId) && (is_array($listExcludedAstrobinId) && 0 < count($listExcludedAstrobinId))) {
            /** @var Query\Match $astrobinMatchQuery */

            foreach ($listExcludedAstrobinId as $astrobinId) {
                $astrobinMatchQuery = new Query\Match();
                $astrobinMatchQuery->setField(self::ASTROBIN_FIELD, $astrobinId);

                $boolQuery->addMustNot($astrobinMatchQuery);
            }
        }

        $query->setQuery($boolQuery);
        $query->setFrom(0)->setSize(500);

        //dump($query->getQuery()->toArray());

        /** @var Search $search */
        $search = new Search($this->client);
        $results = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $results->getTotalHits()) {
            /** @var Document $document */
            foreach($results->getDocuments() as $document) {
                $listAstrobinId[$document->getData()['id']] = $document->getData()['data']['astrobin_id'];
            }
        }
        return $listAstrobinId;
    }


    /**
     * @param int $limit
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getRandomDso(int $limit = 1): \Generator
    {
        /** @var \DateTimeInterface $now */
        $now = new \DateTime();
        $seed = $now->getTimestamp();

        /** @var Query\Exists $existQuery */
        $existQuery = new Query\Exists(self::ASTROBIN_FIELD);

        /** @var Query\FunctionScore $score */
        $score = new Query\FunctionScore();
        $score
            ->setQuery($existQuery)
            ->setBoost(5)
            ->setRandomScore($seed)
            ->setBoostMode(Query\FunctionScore::BOOST_MODE_MULTIPLY);

        /** @var Query $query */
        $query = new Query();
        $query->setFrom(0)->setSize($limit);
        $query->setQuery($score);

        $search = new Search($this->client);
        $results = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $results->count()) {
            /** @var Document $document */
            foreach ($results->getDocuments() as $document) {
                yield $this->buildEntityFromDocument($document);
            }
        }
    }

    /**
     * @return string
     */
    protected function getEntity()
    {
        return 'App\Entity\ES\Dso';
    }

    /**
     * Build a Dso entity from document ElasticSearch
     *
     * @param Document $document
     *
     * @return Dso
     * @throws \ReflectionException
     */
    private function buildEntityFromDocument(Document $document): Dso
    {
        $entity = $this->getEntity();
        /** @var Dso $dso */
        $dso = new $entity;

        $dso->setLocale($this->getLocale())->buildObjectR($document);

        return $dso;
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


