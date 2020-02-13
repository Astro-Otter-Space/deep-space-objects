<?php


namespace App\Entity\ES;

use App\Repository\ObservationRepository;
use Elastica\Document;

/**
 * Class Event
 * @package App\Entity\ES
 */
class Event extends AbstractEntity
{
    private static $listFieldsNoMapping = ['locale', 'fullUrl', 'elasticId'];

    private $id;
    private $locale;
    private $fullUrl;
    private $elasticId;
    private $name;
    private $description;
    private $createdAt;
    private $eventDate;
    private $locationLabel;
    private $location;
    private $tarif;
    private $public;
    private $numberEntrant;
    private $organiserName;
    private $organiserTel;
    private $organiserMail;
    private $shared;
    private $pot2Miel;


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
     * @return Event
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     *
     * @return Event
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
     * @return Event
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
     * @return Event
     */
    public function setElasticId($elasticId)
    {
        $this->elasticId = $elasticId;
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
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return Event
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @param mixed $eventDate
     *
     * @return Event
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationLabel()
    {
        return $this->locationLabel;
    }

    /**
     * @param mixed $locationLabel
     *
     * @return Event
     */
    public function setLocationLabel($locationLabel)
    {
        $this->locationLabel = $locationLabel;
        return $this;
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
     * @return Event
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * @param mixed $tarif
     *
     * @return Event
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * @param mixed $public
     *
     * @return Event
     */
    public function setPublic($public)
    {
        $this->public = $public;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberEntrant()
    {
        return $this->numberEntrant;
    }

    /**
     * @param mixed $numberEntrant
     *
     * @return Event
     */
    public function setNumberEntrant($numberEntrant)
    {
        $this->numberEntrant = $numberEntrant;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganiserName()
    {
        return $this->organiserName;
    }

    /**
     * @param mixed $organiserName
     *
     * @return Event
     */
    public function setOrganiserName($organiserName)
    {
        $this->organiserName = $organiserName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganiserTel()
    {
        return $this->organiserTel;
    }

    /**
     * @param mixed $organiserTel
     *
     * @return Event
     */
    public function setOrganiserTel($organiserTel)
    {
        $this->organiserTel = $organiserTel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganiserMail()
    {
        return $this->organiserMail;
    }

    /**
     * @param mixed $organiserMail
     *
     * @return Event
     */
    public function setOrganiserMail($organiserMail)
    {
        $this->organiserMail = $organiserMail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * @param mixed $shared
     *
     * @return Event
     */
    public function setShared($shared)
    {
        $this->shared = $shared;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPot2Miel()
    {
        return $this->pot2Miel;
    }

    /**
     * @param mixed $pot2Miel
     *
     * @return Event
     */
    public function setPot2Miel($pot2Miel)
    {
        $this->pot2Miel = $pot2Miel;
        return $this;
    }


    /**
     * @return array
     */
    public function getListFieldsNoMapping(): array
    {
        return self::$listFieldsNoMapping;
    }

    /**
     * @return string
     */
    public static function getIndex(): string
    {
        return ObservationRepository::INDEX_NAME;
    }
}