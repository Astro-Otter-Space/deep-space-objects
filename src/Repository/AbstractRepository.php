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

    /**
     * AbstractRepository constructor.
     * @param Client $client
     * @param $locale
     */
    public function __construct(Client $client, $locale)
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
        $search->setQuery($matchQuery);

        /** @var ResultSet $resultSet */
        return $search->search($matchQuery);
    }

    abstract protected function getEntity();
}
