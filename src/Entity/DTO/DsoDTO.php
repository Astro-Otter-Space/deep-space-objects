<?php

namespace App\Entity\DTO;

use App\Entity\ES\Dso;

/**
 * Class DsoDTO
 *
 * @package App\Entity\DTO
 */
final class DsoDTO
{
    /**
     * META
     */
    /** @var  */
    private $id;
    /** @var  */
    private $elasticSearchId;
    /** @var  */
    private $fullUrl;
    /** @var  */
    private $locale;

    /** @var Dso */
    private $dso;

    /**
     * Data
     */
    /**
     * @var
     */
    private $name;
    private $catalogs;
    private $desigs;
    private $alt;
    private $description;
    private $type;
    private $magnitude;
    private $constellation;
    private $distAl;
    private $distPc;
    private $discover;
    private $discoverYear;
    private $astrobin;
    private $geometry;
    private $dim;

    /**
     * DsoDTO constructor.
     *
     * @param Dso $dso
     * @param string $locale
     * @param string $elasticId
     */
    public function __construct(Dso $dso, string $locale, string $elasticId)
    {
        $this->setDso($dso)
            ->setLocale($locale)
            ->setElasticSearchId($elasticId);
    }

    /**
     * @param mixed $locale
     *
     * @return DsoDTO
     */
    public function setLocale($locale): DsoDTO
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @param Dso $dso
     *
     * @return DsoDTO
     */
    public function setDso(Dso $dso): DsoDTO
    {
        $this->dso = $dso;
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
     *
     * @return DsoDTO
     */
    public function setId($id): DsoDTO
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getElasticSearchId()
    {
        return $this->elasticSearchId;
    }

    /**
     * @param mixed $elasticSearchId
     *
     * @return DsoDTO
     */
    public function setElasticSearchId($elasticSearchId): DsoDTO
    {
        $this->elasticSearchId = $elasticSearchId;
        return $this;
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
     *
     * @return DsoDTO
     */
    public function setFullUrl($fullUrl): DsoDTO
    {
        $this->fullUrl = $fullUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return DsoDTO
     */
    public function setName($name): DsoDTO
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCatalogs()
    {
        return $this->catalogs;
    }

    /**
     * @param mixed $catalogs
     *
     * @return DsoDTO
     */
    public function setCatalogs($catalogs): DsoDTO
    {
        $this->catalogs = $catalogs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDesigs()
    {
        return $this->desigs;
    }

    /**
     * @param mixed $desigs
     *
     * @return DsoDTO
     */
    public function setDesigs($desigs): DsoDTO
    {
        $this->desigs = $desigs;
        return $this;
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
     *
     * @return DsoDTO
     */
    public function setAlt($alt): DsoDTO
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return DsoDTO
     */
    public function setDescription($description): DsoDTO
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return DsoDTO
     */
    public function setType($type): DsoDTO
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMagnitude()
    {
        return $this->magnitude;
    }

    /**
     * @param mixed $magnitude
     *
     * @return DsoDTO
     */
    public function setMagnitude($magnitude): DsoDTO
    {
        $this->magnitude = $magnitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConstellation()
    {
        return $this->constellation;
    }

    /**
     * @param mixed $constellation
     *
     * @return DsoDTO
     */
    public function setConstellation($constellation): DsoDTO
    {
        $this->constellation = $constellation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistAl()
    {
        return $this->distAl;
    }

    /**
     * @param mixed $distAl
     *
     * @return DsoDTO
     */
    public function setDistAl($distAl): DsoDTO
    {
        $this->distAl = $distAl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistPc()
    {
        return $this->distPc;
    }

    /**
     * @param mixed $distPc
     *
     * @return DsoDTO
     */
    public function setDistPc($distPc): DsoDTO
    {
        $this->distPc = $distPc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscover()
    {
        return $this->discover;
    }

    /**
     * @param mixed $discover
     *
     * @return DsoDTO
     */
    public function setDiscover($discover): DsoDTO
    {
        $this->discover = $discover;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscoverYear()
    {
        return $this->discoverYear;
    }

    /**
     * @param mixed $discoverYear
     *
     * @return DsoDTO
     */
    public function setDiscoverYear($discoverYear): DsoDTO
    {
        $this->discoverYear = $discoverYear;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAstrobin()
    {
        return $this->astrobin;
    }

    /**
     * @param mixed $astrobin
     *
     * @return DsoDTO
     */
    public function setAstrobin($astrobin): DsoDTO
    {
        $this->astrobin = $astrobin;
        return $this;
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
     *
     * @return DsoDTO
     */
    public function setGeometry($geometry): DsoDTO
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDim()
    {
        return $this->dim;
    }

    /**
     * @param mixed $dim
     *
     * @return DsoDTO
     */
    public function setDim($dim): DsoDTO
    {
        $this->dim = $dim;
        return $this;
    }

}
