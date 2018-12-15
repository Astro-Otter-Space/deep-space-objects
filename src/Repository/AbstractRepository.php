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
     * @param Search $search
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
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
     * @param $id
     * @return ResultSet
     */
    protected function findById($id)
    {
        /** @var Constellation|AbstractEntity $entity */
        $entityName = $this->getEntity();
        $entity = new $entityName;

        $this->client->addIndex($entity::getIndex());

        $term = new Query\Term();
        $term->setTerm('id', $id);

        $this->client->setQuery($term);

        /** @var ResultSet $resultSet */
        return $this->search->search();
    }


    abstract protected function getEntity();
}