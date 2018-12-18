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

        $this->client->getIndex($entity::getIndex());
        dump($this->client);

        /** @var Query\Term $term */
        $term = new Query\Term();
        $term->setTerm('id', $id);

        /** @var Search $search */
        $search = new Search($this->client);
        $search->setQuery($term);

        /** @var ResultSet $resultSet */
        return $search->search();
    }

    /**  */
    abstract protected function getEntity();
}
