<?php

namespace App\Repository;

use App\Entity\AbstractEntity;
use App\Entity\Constellation;
use Elastica\Client;
use Elastica\Query;
use Elastica\ResultSet;
use Elastica\Search;

/**
 * Class AbstractRepository
 * @package App\Repository
 */
abstract class AbstractRepository
{
    protected $locale;

    /** @var Search  */
    protected $client;

    const FROM = 0;
    const SIZE = 20;

    /**
     * AbstractRepository constructor.
     * @param Client $client
     * @param $locale
     */
    public function __construct(Client $client, $locale = 'en')
    {
        $this->client = $client;
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    /**
     * Return document by Id
     * @param $id
     * @return ResultSet
     */
    protected function findById($id): ResultSet
    {
        /** @var Constellation|AbstractEntity $entity */
        $entityName = $this->getEntity();
        $entity = new $entityName;

        $this->client->getIndex($entity::getIndex());

        /** @var Query\Term $term */
        $matchQuery = new Query\Match();
        $matchQuery->setField('id', $id);

        /** @var Search $search */
        $search = new Search($this->client);

        /** @var ResultSet $resultSet */
        return $search->search($matchQuery);
    }


    /**
     * @param $searchTerm
     * @param $listSearchFields
     * @return ResultSet
     */
    public function requestBySearchTerms($searchTerm, $listSearchFields)
    {

        $this->client->getIndex($this->getType());

        /** @var Query\MultiMatch $query */
        $query = new Query\MultiMatch();
        $query->setFields($listSearchFields);
        $query->setQuery($searchTerm);
        $query->setType('phrase_prefix');

        /** @var Search $search */
        $search = new Search($this->client);
        $search->addIndex($this->getType());

        return $search->search($query);
    }

    abstract protected function getEntity();

    abstract protected function getType();
}
