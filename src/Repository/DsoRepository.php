<?php

namespace App\Repository;

use App\Classes\Utils;
use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use Elastica\Aggregation\Range;
use Elastica\Aggregation\Terms;
use Elastica\Document;
use Elastica\Query;
use Elastica\Result;
use Elastica\ResultSet;
use Elastica\Search;

/**
 * Class DsoRepository
 * @package App\Repository
 */
class DsoRepository extends AbstractRepository
{
    use SymfonyServicesTrait;
    private static array $listSearchFields = [
        'id',
        'desigs',
        'alt.alt',
        'discover'
    ];

    private static array $listAggregates = [
        'constellation' => [
            'field' => 'const_id.keyword',
            'size' => 100
        ],
        'catalog' => [
            'field' => 'catalog.keyword',
            'size' => 100
        ],
        'type' => [
            'field' => 'type.keyword',
            'size' => 100
        ]
    ];

    private static array $listAggregatesRange = [
        'magnitude' => [
            'field' => 'mag',
            'ranges' => [
                ['to' => 5, 'key' => 'low'],
                ['from' => 5, 'to' => 10, 'key' => 'average'],
                ['from' => 10, 'to' => 15, 'key' => 'high'],
                ['from' => 15, 'key' => 'hard']
            ]
        ]
    ];

    public const INDEX_NAME = 'deepspaceobjects';

    public const ASTROBIN_FIELD = 'astrobin_id';

    /**
     * Get aggregates proprieties
     *
     * @param bool $onlyKeys
     *
     * @return array
     */
    public function getListAggregates(bool $onlyKeys): array
    {
        if ($onlyKeys) {
            return array_merge(array_keys(self::$listAggregates), array_keys(self::$listAggregatesRange));
        }

        return self::$listAggregates;
    }

    /**
     * Retrieve object by his Id
     *
     * @param string $id
     *
     * @return DTOInterface|null
     * @throws \JsonException
     */
    public function getObjectById(string $id): ?DTOInterface
    {
        /** @var ResultSet $resultDocument */
        $resultDocument = $this->findById(ucfirst($id));
        if (0 < $resultDocument->getTotalHits()) {
            $document = $resultDocument->getDocuments()[0];
            return $this->buildEntityFromDocument($document);
        }
        return null;
    }

    /**
     * Retrieve  list of Dso objects in a constellation
     *
     * @param string $constId
     * @param string|null $excludedId
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return array
     */
    public function getObjectsByConstId(string $constId, ?string $excludedId, ?int $offset, ?int $limit): array
    {
        if (is_null($offset )) {
            $offset = parent::FROM;
        }

        if (is_null($limit)) {
            $limit = (int)parent::SIZE;
        }

        $this->client->getIndex(self::INDEX_NAME);

        /** @var Query $query */
        $query = new Query();

        /** @var Query\Term $mustQuery */
        $mustQuery = new Query\Term();
        $mustQuery->setTerm('const_id', strtolower($constId));

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
                'mag' => ['order' => parent::SORT_ASC, 'mode' => 'avg'],
            ]
        );

        /** @var Search $search */
        $search = new Search($this->client);
        $search = $search->addIndexByName(self::INDEX_NAME)->search($query);

        $listDsoId = [];
        if (0 < $search->count()) {
            foreach ($search->getDocuments() as $document) {
                $listDsoId[] = $document->getData()['id'];
            }
        }

        return $listDsoId;
    }

    /**
     * Get last updated items
     * @return array
     */
    public function getLastUpdated(): array
    {
        /** @var Query $query */
        $query = new Query();

        $query->setFrom(0)->setSize(self::SIZE);
        $query->addSort([
            'updated_at' => ['order' => parent::SORT_DESC]
        ]);

        $search = new Search($this->client);
        $result = $search->addIndexByName(self::INDEX_NAME)->search($query);

        return array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $result->getResults());
    }

    /**
     * Search autocomplete
     *
     * @param string $searchTerm
     *
     * @return array
     */
    public function getObjectsBySearchTerms(string $searchTerm): array
    {
        if ('en' !== $this->getLocale()) {
            self::$listSearchFields[] = sprintf('alt_%s', $this->getLocale());
            self::$listSearchFields[] = sprintf('alt_%s.keyword', $this->getLocale());
        }

        $result = $this->requestBySearchTerms($searchTerm, self::$listSearchFields);

        return array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $result->getResults());
    }

    /**
     * Catalog Research, with|without filters
     * Get aggregates
     *
     * @param int $from
     * @param array $filters
     * @param int|null $to
     * @param bool $hydrate
     *
     * @return array
     */
    public function getObjectsCatalogByFilters(int $from, array $filters, ?int $to, ?bool $hydrate): array
    {
        $this->client->getIndex(self::INDEX_NAME);
        $size = (is_null($to)) ? parent::SIZE : $to;

        $query = new Query();

        // BUILD FILTERS
        if (0 < count($filters)) {
            /** @var Query\BoolQuery $query */
            $boolQuery = new Query\BoolQuery();

            // Add filters
            foreach ($filters as $type => $val) {
                $mustQuery = new Query\Term();
                $rangeQuery = new Query\Range();
                $field = ('magnitude' === $type) ? self::$listAggregatesRange[$type]['field'] : self::$listAggregates[$type]['field'];
                if ('magnitude' === $type) {
                    $keyRange = array_search($val, array_column(self::$listAggregatesRange[$type]['ranges'], 'key'), true);
                    $range = self::$listAggregatesRange[$type]['ranges'][$keyRange];

                    if (array_key_exists('to', $range)) {
                        $paramRange['lte'] = $range['to'];
                    }
                    if (array_key_exists('from', $range)) {
                        $paramRange['gte'] = $range['from'];
                    }

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
            'id.raw' => [
                'order' => parent::SORT_ASC
            ]
        ]);

        // Aggregates
        array_walk(self::$listAggregates, static function($tab, $type) use($query) {
            $aggregation = new Terms($type);
            $aggregation->setField($tab['field']);
            $aggregation->setSize($tab['size']);

            $query->addAggregation($aggregation);
        });

        // Aggregates range
        array_walk(self::$listAggregatesRange, static function ($tab, $type) use ($query){
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

        $search = new Search($this->client);
        $results = $search->addIndexByName(self::INDEX_NAME)->search($query);
        $nbItems = $results->getTotalHits();

        if (false === $hydrate) {
            return [$results->getDocuments(), $nbItems];
        }

        $listDsoId = array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $results->getResults());

        $listAggregations = [];
        foreach ($results->getAggregations() as $type => $aggregations) {
            $listAggregations[$type] = array_map(function($item) use($type) {
                return [
                    'name' => $item['key'],
                    'count' => $item['doc_count'],
                    'label' => sprintf('%s (%s)', $this->translator->trans(sprintf('%s.%s', $type, strtolower($item['key']))), $item['doc_count'])
                ];
            }, $aggregations['buckets']);
        }

        $listSort = $this->getListAggregates(true);
        uksort($listAggregations, static function ($k1, $k2) use ($listSort) {
            return ((array_search($k1, $listSort, true) > array_search($k2, $listSort, true)) ? 1 : -1);
        });

        return [$listDsoId, $listAggregations, $nbItems];
    }

    /**
     * Retrieve last updated Dso
     *
     * @param \DateTimeInterface $lastUpdate
     * @return array
     */
    public function getUpdatedAfter(\DateTimeInterface $lastUpdate): array
    {
        $this->client->getIndex(self::INDEX_NAME);

        $now = new \DateTime('now');
        $now->setTimezone(new \DateTimeZone('Europe/paris'));

        $query = new Query();

        /** @var Query\BoolQuery $boolQuery */
        $boolQuery = new Query\BoolQuery();

        /** @var Range $range */
        $rangeQuery = new Query\Range();
        $rangeQuery->addField('updated_at', [
            'gte' => $lastUpdate->format(Utils::FORMAT_DATE_ES),
            //'lt' => $now->format(Utils::FORMAT_DATE_ES)
        ]);


        $boolQuery->addMust($rangeQuery);
        $query->setQuery($boolQuery);

        $query->setFrom(0)->setSize(self::MAX_SIZE);

        $search = new Search($this->client);
        $results = $search->addIndexByName(self::INDEX_NAME)->search($query);

        return array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $results->getResults());
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
     *            "field": "astrobin_id"
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
        $results = $search->addIndexByName(self::INDEX_NAME)->search($query);

        if (0 < $results->getTotalHits()) {
            /** @var Document $document */
            foreach($results->getDocuments() as $document) {
                $dataDocument = $document->getData();
                $listAstrobinId[$dataDocument['id']] = $dataDocument['astrobin_id'];
            }
        }
        return $listAstrobinId;
    }

    /**
     * get random Dso
     *
     * @param int $limit
     * @return array
     */
    public function getRandomDso(int $limit = 1): array
    {
        $listDsoId = [];

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
        $results = $search->addIndexByName(self::INDEX_NAME)->search($query);

        return array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $results->getResults());
    }

    /**
     * Build a Dso entity from document ElasticSearch
     *
     * @param Document $document
     *
     * @return DTOInterface
     * @throws \JsonException
     */
    private function buildEntityFromDocument(Document $document): DTOInterface
    {
        return $this->buildDTO($document);
    }

    /**
     * @return string
     */
    protected function getEntity(): string
    {
        return Dso::class;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        return self::INDEX_NAME;
    }


    /**
     * @return string
     */
    protected function getDTO(): string
    {
        return DsoDTO::class;
    }

    /**
    $aggregates = [
        'aggregates' => [
            'type' => [
                'terms' => [
                    'field' => 'type.keyword',
                    'size' => 20
                ]
            ],
            'const_id' => [
                'terms' => [
                'field' => 'const_id.keyword',
                'size' => 100
                ]
            ],
            'mag' => [
                'range' => [
                    'field' => 'mag',
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
