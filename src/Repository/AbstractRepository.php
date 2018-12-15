<?php

namespace App\Repository;

use App\Entity\AbstractEntity;
use App\Entity\Constellation;
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
    protected $search;

    /** @var Query  */
    protected $query;

    /**
     * AbstractRepository constructor.
     * @param Search $search
     * @param Query $query
     */
    public function __construct(Search $search, Query $query)
    {
        $this->search = $search;
        $this->query = $query;
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

        $this->search->addIndex($entity::getIndex());

        $query = $this->query->setFrom(0)
            ->setSize(1)
            ->setQuery(['id' => $id]);

        $this->search->setQuery($query);

        /** @var ResultSet $resultSet */
        return $this->search->search();
    }


    abstract protected function getEntity();
}