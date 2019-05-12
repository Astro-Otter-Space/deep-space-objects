<?php

namespace App\Entity;

use App\Classes\Utils;
use App\Repository\ObservationRepository;
use Symfony\Component\Validator\Constraint as Assert;

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

    /**
     * @var string|null
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     */
    private $username;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     */
    private $name;

    /**
     * @var
     */
    private $description;

    /**
     * @var
     *
     */
    private $createdAt;

    /**
     * @var
     */
    private $isPublic;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     * @Assert\DateTime(message="", validation_groups={"add_observation"})
     */
    private $observationDate;
    /** @var  */

    private $location;

    /** @var ListDso|array  */
    private $dsoList;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     */
    private $instrument;

    /**
     * @var integer|null
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     * @Assert\Regex(pattern="/\d/", match=true, validation_groups={"add_observation"})
     */
    private $diameter;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     * @Assert\Regex(pattern="/\d/", match=true, validation_groups={"add_observation"})
     */
    private $focal;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     */
    private $mount;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", validation_groups={"add_observation"})
     */
    private $ocular;

    /**
     * @var
     * @Assert\Blank(message="contact.constraint.invalid_form")
     */
    private $pot2Miel;

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
     * @return string|null
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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
     * @return \DateTime
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = \DateTime::createFromFormat("Y-m-dTH:i:sZ", $createdAt);
    }

    /**
     * @return mixed
     */
    public function getIsPublic()
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
     * @return ListDso|array
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
     * @return integer|null
     */
    public function getDiameter(): ?int
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
    public function getOcular()
    {
        if (!is_array($this->ocular)) {
            $this->ocular = [$this->ocular];
        }
        return $this->ocular;
    }

    /**
     * @param mixed $ocular
     */
    public function setOcular($ocular): void
    {
        $this->ocular = $ocular;
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
     */
    public function setPot2Miel($pot2Miel): void
    {
        $this->pot2Miel = $pot2Miel;
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
            'report' => Utils::numberFormatByLocale($this->getFocal()/$this->getDiameter()),
            'mount' => $this->getMount(),
            'ocular' => implode(self::DATA_CONCAT_GLUE, $this->getOcular())
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }
}
