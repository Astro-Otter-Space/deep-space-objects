<?php

namespace App\Entity;

use App\Repository\ConstellationRepository;

/**
 * Class Constellation
 * @package App\Entity
 */
class Constellation extends AbstractEntity
{
    private $locale;

    private $elasticId;

    /** @var  */
    private $id;

    /** @var  */
    private $gen;

    /** @var  */
    private $alt;

    /** @var  */
    private $rank;

    /** @var  */
    private $loc;

    /** @var  */
    private $geometry;

    /** @var  */
    private $geometryLine;

    private $listDso;

    private $fullUrl;

    private $map;

    private static $listFieldsNoMapping = ['elasticId', 'locale', 'geometry', 'geometryLine', 'fullUrl', 'listDso', 'map'];

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param $locale
     * @return Constellation
     */
    public function setLocale($locale): Constellation
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getGen()
    {
        return $this->gen;
    }

    /**
     * @param mixed $gen
     */
    public function setGen($gen): void
    {
        $this->gen = $gen;
    }

    /**
     * @return mixed
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     */
    public function setAlt($alt): void
    {
        if (!$this->locale || 'en' === $this->locale) {
            $this->alt = $alt['alt'];
        } else {
            $this->alt = $alt[sprintf('alt_%s', $this->locale)];
        }
    }

    /**
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return mixed
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param mixed $loc
     */
    public function setLoc($loc): void
    {
        $this->loc = $loc;
    }

    /**
     * @return mixed
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param mixed $geometry
     */
    public function setGeometry($geometry): void
    {
        $this->geometry = $geometry;
    }

    /**
     * @return mixed
     */
    public function getGeometryLine()
    {
        return $this->geometryLine;
    }

    /**
     * @param mixed $geometryLine
     */
    public function setGeometryLine($geometryLine): void
    {
        $this->geometryLine = $geometryLine;
    }

    /**
     * @return string
     */
    public static function getIndex()
    {
        return ConstellationRepository::INDEX_NAME;
    }

    /**
     * @return mixed
     */
    public function getElasticId()
    {
        return $this->elasticId;
    }

    /**
     * @param mixed $elasticId
     */
    public function setElasticId($elasticId): void
    {
        $this->elasticId = $elasticId;
    }

    /**
     * @return ListDso
     */
    public function getListDso()
    {
        return $this->listDso->getIterator();
    }

    /**
     * @param mixed $listDso
     */
    public function setListDso(ListDso $listDso): void
    {
        $this->listDso = $listDso;
    }

    /**
     * @return mixed
     */
    public function getFullUrl()
    {
        return $this->fullUrl;
    }

    /**
     * @param mixed $fullUrl
     */
    public function setFullUrl($fullUrl): void
    {
        $this->fullUrl = $fullUrl;
    }

    /**
     * @return mixed
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param mixed $map
     */
    public function setMap($map): void
    {
        $this->map = $map;
    }

    /**
     * @return array
     */
    public function getListFieldsNoMapping()
    {
        return self::$listFieldsNoMapping;
    }

}
