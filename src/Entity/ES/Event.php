<?php


namespace App\Entity\ES;

use App\Classes\Utils;
use App\Repository\ObservationRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Event
 * @package App\Entity\ES
 */
class Event extends AbstractEntity
{
    private static $listFieldsNoMapping = ['locale', 'fullUrl', 'elasticId'];

    private static $fieldsObjectToJson = ['id', 'name', 'description', 'eventDate', 'createdAt', 'locationLabel', 'location', 'tarif', 'public', 'numberEntrant', 'organiserName', 'organiserTel', 'organiserMail'];

    /** @var string */
    private $id;
    /** @var string */
    private $locale;
    /** @var string */
    private $fullUrl;
    /** @var string */
    private $elasticId;

    /**
     * @var
     * @Assert\NotBlank(message="", groups={"add_event"})
     */
    private $name;

    /**
     * @var
     * @Assert\NotBlank(message="", groups={"add_event"})
     */
    private $description;

    /** @var
     * @Assert\DateTime(message="", groups={"add_event"})
     */
    private $createdAt;

    /**
     * @var
     * @Assert\NotBlank(message="", groups={"add_event"})
     */
    private $eventDate;

    /**
     * @var
     * @Assert\NotBlank(message="", groups={"add_event"})
     */
    private $locationLabel;

    /**
     * @var
     */
    private $location;

    /**
     * @var
     */
    private $tarif;

    /**
     * @var
     */
    private $public;

    /**
     * @var
     */
    private $numberEntrant;

    /**
     * @var
     * @Assert\NotBlank(message="", groups={"add_event"})
     */
    private $organiserName;

    /**
     * @var
     */
    private $organiserTel;

    /**
     * @var
     */
    private $organiserMail;

    /**
     * @var
     */
    private $shared;

    /**
     * @var
     * @Assert\Blank(message="contact.constraint.invalid_form", groups={"add_event"})
     */
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
     * @param bool $convertInString
     *
     * @return Event
     */
    public function setCreatedAt($createdAt, $convertInString)
    {
        if (is_string($createdAt) && !is_null($createdAt)) {
            if (true === $convertInString) {
                $this->createdAt = $createdAt;
            } else {
                $this->createdAt = \DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $createdAt);
            }

        } else if ($createdAt instanceof \DateTime) {
            $this->createdAt = $createdAt;
        }

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
     * @param bool $convertInString
     *
     * @return Event
     */
    public function setEventDate($eventDate, $convertInString = true): self
    {
        if (is_string($eventDate) && !is_null($eventDate)) {
            if (true === $convertInString) {
                $this->eventDate = (string)$eventDate;
            } else {
                $this->eventDate = \DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $eventDate);
            }

        } else if ($eventDate instanceof \DateTime) {
            $this->eventDate = $eventDate;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocationLabel():? string
    {
        return $this->locationLabel;
    }

    /**
     * @param mixed $locationLabel
     *
     * @return Event
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
    public function getTarif():? float
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
    public function getPublic():? string
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
    public function getNumberEntrant():? int
    {
        return $this->numberEntrant;
    }

    /**
     * @param mixed $numberEntrant
     *
     * @return Event
     */
    public function setNumberEntrant($numberEntrant): self
    {
        $this->numberEntrant = $numberEntrant;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganiserName():? string
    {
        return $this->organiserName;
    }

    /**
     * @param mixed $organiserName
     *
     * @return Event
     */
    public function setOrganiserName($organiserName): self
    {
        $this->organiserName = $organiserName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganiserTel():? string
    {
        return $this->organiserTel;
    }

    /**
     * @param mixed $organiserTel
     *
     * @return Event
     */
    public function setOrganiserTel($organiserTel): self
    {
        $this->organiserTel = $organiserTel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganiserMail():? string
    {
        return $this->organiserMail;
    }

    /**
     * @param mixed $organiserMail
     *
     * @return Event
     */
    public function setOrganiserMail($organiserMail): self
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
    public function setShared($shared): self
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
    public function setPot2Miel($pot2Miel): self
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
}
