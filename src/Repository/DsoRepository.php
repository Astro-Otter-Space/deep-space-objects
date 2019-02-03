<?php

namespace App\Repository;

use App\Entity\Dso;
use App\Entity\ListDso;
use App\Managers\DsoManager;
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
        'id.keyword',
        'data.desigs',
        'data.desigs.keyword',
        'data.alt.alt',
        'data.const_id'
    ];

    const INDEX_NAME = 'deepspaceobjects';

    /**
     * DsoRepository constructor.
     * @param Client $client
     * @param $locale
     */
    public function __construct(Client $client, $locale)
    {
        parent::__construct($client, $locale);
    }


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
        $query->setFrom(0)->setSize($limit);

        $search = new Search($this->client);
        $search = $search->search($query);

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
        $this->client->getIndex(self::INDEX_NAME);

        if ('en' !== $this->getLocale()) {
            array_push(self::$listSearchFields, sprintf('data.alt.alt_%s', $this->getLocale()));
        }

        /** @var Query\MultiMatch $query */
        $query = new Query\MultiMatch();
        $query->setFields(self::$listSearchFields);
        $query->setQuery($searchTerm);

        /** @var Search $search */
        $search = new Search($this->client);
        $result = $search->search($query);

//        dump(json_encode($result->getQuery()->toArray()));

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


