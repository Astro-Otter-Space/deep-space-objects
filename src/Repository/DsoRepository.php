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

/**
 * Class DsoRepository
 * @package App\Repository
 */
class DsoRepository extends AbstractRepository
{

    private static $listSearchFields = [
        'id',
        'data.desigs',
        'data.alt',
        "data.const_id"
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
     * Retrieve list of Dso objects in a constellation
     * @param $constId
     * @param $limit
     * @return ListDso $dsoList
     */
    public function getObjectsByConstId($constId, $limit): ListDso
    {
        /** @var ListDso $dsoList */
        $dsoList = new ListDso();
        $this->client->getIndex(self::INDEX_NAME);

        /** @var Query\Term $term */
        $matchQuery = new Query\Match();
        $matchQuery->setField('data.const_id', $constId);

        /** @var Query\Limit $limitQuery */
        $limitQuery = new Query\Limit($limit);

        /** @var Search $search */
        $search = new Search($this->client);
        $search->setQuery($matchQuery);

        $result = $search->search($matchQuery);
        if (0 < $result->count()) {
            foreach ($result->getDocuments() as $document) {
                $dso = $this->buildEntityFromDocument($document);
                $dsoList->addDso($dso);
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
    public function gteObjectsBySearchTerms($searchTerm)
    {
        $list = [];
        $this->client->getIndex(self::INDEX_NAME);

        array_push(self::$listSearchFields, sprintf('data.alt_%s', $this->getLocale()));

        /** @var Query\QueryString $query */
        $query = new Query\QueryString();
        $query->setFields(self::$listSearchFields);
        $query->setQuery($searchTerm);

        /** @var Search $search */
        $search = new Search($this->client);
        $result = $search->search($query);

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
     */
    private function buildEntityFromDocument(Document $document)
    {
        $entity = $this->getEntity();
        /** @var Dso $dso */
        $dso = new $entity;

        $dso = $dso->setLocale($this->getLocale())->buildObject($document);

        return $dso;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return self::INDEX_NAME;
    }
}
