<?php

namespace App\Entity;

use App\Repository\ObservationRepository;

/**
 * Class Observation
 *
 * @package App\Entity
 */
class Observation extends AbstractEntity
{
    /** @var  */
    private $locale;
    /** @var  */
    private $fullUrl;
    /** @var  */
    private $elasticId;
    /** @var  */
    private $id;
    /** @var  */
    private $username;
    /** @var  */
    private $name;
    /** @var \DateTime */
    private $createdAt;
    /** @var  */
    private $isPublic;
    /** @var \DateTime */
    private $observationDate;
    /** @var  */
    private $location;
    /** @var  */
    private $dsoList;

    private $instrument;
    private $diameter;
    private $focal;
    private $rapport;
    private $mount;
    private $occular;

    private static $listFieldsNoMapping = ['locale', 'fullUrl', 'elasticId'];

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     *
     * @return Observation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
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
     * @return Observation
     */
    public function setFullUrl($fullUrl)
    {
        $this->fullUrl = $fullUrl;
        return $this;
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
     *
     * @return Observation
     */
    public function setElasticId($elasticId)
    {
        $this->elasticId = $elasticId;
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
     * @return Observation
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     *
     * @return Observation
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * @return Observation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return Observation
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = \DateTime::createFromFormat("Y-m-dTH:i:sZ" ,$createdAt);
    }

    /**
     * @return mixed
     */
    public function getisPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $isPublic
     *
     * @return Observation
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObservationDate()
    {
        return $this->observationDate;
    }

    /**
     * @param mixed $observationDate
     *
     * @return Observation
     */
    public function setObservationDate($observationDate)
    {
        $this->observationDate = \DateTime::createFromFormat("Y-m-d", $observationDate);
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     *
     * @return Observation
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return ListDso
     */
    public function getDsoList()
    {
        return $this->dsoList;
    }

    /**
     * @param mixed $dsoList
     *
     * @return Observation
     */
    public function setDsoList(ListDso $dsoList)
    {
        $this->dsoList = $dsoList;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstrument()
    {
        return $this->instrument;
    }

    /**
     * @param mixed $instrument
     */
    public function setInstrument($instrument): void
    {
        $this->instrument = $instrument;
    }

    /**
     * @return mixed
     */
    public function getDiameter(): int
    {
        return $this->diameter;
    }

    /**
     * @param mixed $diameter
     */
    public function setDiameter($diameter): void
    {
        $this->diameter = $diameter;
    }

    /**
     * @return mixed
     */
    public function getFocal(): int
    {
        return $this->focal;
    }

    /**
     * @param mixed $focal
     */
    public function setFocal($focal): void
    {
        $this->focal = $focal;
    }

    /**
     * @return mixed
     */
    public function getRapport(): int
    {
        return ($this->getDiameter()/$this->getFocal());
    }


    /**
     * @return mixed
     */
    public function getMount()
    {
        return $this->mount;
    }

    /**
     * @param mixed $mount
     */
    public function setMount($mount): void
    {
        $this->mount = $mount;
    }

    /**
     * @return mixed
     */
    public function getOccular()
    {
        return $this->occular;
    }

    /**
     * @param mixed $occular
     */
    public function setOccular($occular): void
    {
        $this->occular = $occular;
    }



    /**
     * @return array
     */
    public function getListFieldsNoMapping()
    {
        return self::$listFieldsNoMapping;
    }

    /**
     * @return string
     */
    public static function getIndex()
    {
        return ObservationRepository::INDEX_NAME;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $data = [
            'instrument' => $this->getInstrument(),
            'diameter' => $this->getDiameter(),
            'focal' => $this->getFocal(),
            'rapport' => $this->getRapport(),
            'mount' => $this->getMount(),
            'occulat' => implode(self::DATA_CONCAT_GLUE, $this->getOccular())
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }
}
