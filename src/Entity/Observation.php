<?php

namespace App\Entity;

use App\Classes\Utils;
use App\Repository\ObservationRepository;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     */
    private $username;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
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
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     */
    private $observationDate;
    /** @var  */

    /** @var  */
    private $location;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     */
    private $locationLabel;

    /** @var ListDso|array|null */
    private $dsoList;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     */
    private $instrument;

    /**
     * @var integer|null
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     * @Assert\Regex(pattern="/\d/", match=true, groups={"add_observation"})
     */
    private $diameter;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     * @Assert\Regex(pattern="/\d/", match=true, groups={"add_observation"})
     */
    private $focal;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     */
    private $mount;

    /**
     * @var array|string
     * @Assert\NotBlank(message="contact.constraint.not_blank", groups={"add_observation"})
     */
    private $ocular;

    /**
     * @var
     * @Assert\Blank(message="contact.constraint.invalid_form", groups={"add_observation"})
     */
    private $pot2Miel;

    private static $listFieldsNoMapping = ['locale', 'fullUrl', 'elasticId'];
    private static $fieldsObjectToJson = ['id', 'username', 'name', 'description', 'createdAt', 'isPublic', 'observationDate', 'locationLabel', 'location', 'dsoList', 'intrument', 'diameter', 'focal', 'mount', 'ocular'];

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
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     *
     * @return Observation
     */
    public function setUsername($username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Observation
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return Observation
     */
    public function setDescription($description): self
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
     * @return Observation
     */
    public function setCreatedAt($createdAt): self
    {
        if (is_string($createdAt) && !is_null($createdAt)) {
            $this->createdAt = \DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $createdAt);
        } else if ($createdAt instanceof \DateTime) {
            $this->createdAt = $createdAt->format(Utils::FORMAT_DATE_ES);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $isPublic
     *
     * @return Observation
     */
    public function setIsPublic($isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
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
    public function setObservationDate($observationDate): self
    {
        if (is_string($observationDate) && !is_null($observationDate)) {
            $this->observationDate = \DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $observationDate);
        } else if ($observationDate instanceof \DateTime) {
            $this->observationDate = $observationDate->format(Utils::FORMAT_DATE_ES);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationLabel(): ?string
    {
        return $this->locationLabel;
    }

    /**
     * @param string $locationLabel
     *
     * @return Observation
     */
    public function setLocationLabel($locationLabel): self
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
     * @return Observation
     */
    public function setLocation($location): self
    {
        $this->location = $location;
        return $this;
    }


    /**
     * @return ListDso|array|null
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
    public function setDsoList(ListDso $dsoList): self
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
     *
     * @return Observation
     */
    public function setInstrument($instrument): self
    {
        $this->instrument = $instrument;
        return $this;
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
     *
     * @return Observation
     */
    public function setDiameter($diameter): self
    {
        $this->diameter = $diameter;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFocal(): ?int
    {
        return $this->focal;
    }

    /**
     * @param mixed $focal
     *
     * @return Observation
     */
    public function setFocal($focal): self
    {
        $this->focal = $focal;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getMount(): ?string
    {
        return $this->mount;
    }

    /**
     * @param mixed $mount
     *
     * @return Observation
     */
    public function setMount($mount): self
    {
        $this->mount = $mount;
        return $this;
    }

    /**
     * @return array|string|null
     */
    public function getOcular(): ?array
    {
        if (!is_array($this->ocular)) {
            $this->ocular = [$this->ocular];
        }
        return $this->ocular;
    }

    /**
     * @param mixed $ocular
     *
     * @return Observation
     */
    public function setOcular($ocular): self
    {
        $this->ocular = (!is_array($ocular)) ? [$ocular]: $ocular;
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
     */
    public function setPot2Miel($pot2Miel): void
    {
        $this->pot2Miel = $pot2Miel;
    }

    /**
     * @return array
     */
    public function getListFieldsNoMapping(): array
    {
        return self::$listFieldsNoMapping;
    }

    /**
     * @return array
     */
    public function getFieldsObjectToJson(): array
    {
        return self::$fieldsObjectToJson;
    }

    /**
     * @return string
     */
    public static function getIndex(): string
    {
        return ObservationRepository::INDEX_NAME;
    }


    /**
     * @return array
     */
    public function toArray(): array
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
