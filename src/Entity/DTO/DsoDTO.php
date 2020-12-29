<?php

namespace App\Entity\DTO;

use App\Classes\Utils;
use App\Entity\ES\Dso;
use AstrobinWs\Response\Image;

/**
 * Class DsoDTO
 *
 * @package App\Entity\DTO
 */
final class DsoDTO implements DTOInterface
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

    private $updatedAt;

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
    private $constellationId;
    private $constellation;
    private $distAl;
    private $discover;
    private $discoverYear;
    private $astrobinId;
    private $astrobin;
    private $geometry;
    private $dim;
    private $declinaison;
    private $rightAscencion;

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
            ->setElasticSearchId($elasticId)
            ->setId(strtolower($dso->getId()))
            ->setAlt($dso->getAlt())
            ->setAstrobinId($dso->getAstrobinId())
            ->setConstellationId($dso->getConstId())
            ->setCatalogs($dso->getCatalog())
            ->setDesigs($dso->getDesigs())
            ->setDeclinaison($dso->getDec())
            ->setDescription($dso->getDescription())
            ->setDesigs($dso->getDesigs())
            ->setDim($dso->getDim())
            ->setDiscover($dso->getDiscover())
            ->setDiscoverYear($dso->getDiscoverYear())
            ->setDistAl($dso->getDistAl())
            ->setGeometry($dso->getGeometry())
            ->setMagnitude($dso->getMag())
            ->setRightAscencion($dso->getRa())
            ->setType($dso->getType())
        ;
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
     * @param mixed $elasticSearchId
     *
     * @return DsoDTO
     */
    public function setElasticSearchId($elasticSearchId): DsoDTO
    {
        $this->elasticSearchId = $elasticSearchId;
        return $this;
    }

    public function guid(): string
    {
        return md5(sprintf('%s_%s', $this->getId(), $this->locale));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Fist we retrieve desigs and other desigs
        $desig = (is_array($this->getDesigs())) ? current($this->getDesigs()) : $this->getDesigs();

        // If Alt is set, we merge desig and alt

        $fieldAlt = ('en' !== $this->locale) ? sprintf('alt_%s', $this->locale): 'alt';
        $title = (empty($this->getAlt()[$fieldAlt]))
            ? $desig
            : implode (Utils::DATA_CONCAT_GLUE, [$this->getAlt()[$fieldAlt], $desig]);

        // If title still empty, we put Id
        $title = (empty($title))
            ? $this->getId()
            : $title;

        return $title;
    }

    /**
     * @return mixed
     */
    public function fullUrl(): string
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
     * @return array|string|null
     */
    public function getCatalogs()
    {
        return $this->catalogs;
    }

    /**
     * @param array|string|null $catalogs
     *
     * @return DsoDTO
     */
    public function setCatalogs($catalogs): DsoDTO
    {
        $this->catalogs = $catalogs;
        return $this;
    }

    /**
     * @return array
     */
    public function getDesigs(): array
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
        return sprintf('type.%s', $this->type);
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
    public function getConstellationId(): string
    {
        return $this->constellationId;
    }

    /**
     * @param mixed $constellationId
     *
     * @return DsoDTO
     */
    public function setConstellationId(string $constellationId): self
    {
        $this->constellationId = $constellationId;
        return $this;
    }

    /**
     * @return null|ConstellationDTO|DTOInterface
     */
    public function getConstellation(): ?DTOInterface
    {
        return $this->constellation;
    }

    /**
     * @param mixed $constellation
     *
     * @return DsoDTO
     */
    public function setConstellation(DTOInterface $constellation): DsoDTO
    {
        $this->constellation = $constellation;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getDistAl()
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

    public function distanceLightYears()
    {
        return Utils::numberFormatByLocale($this->distAl);
    }

    /**
     * @return mixed
     */
    public function distanceParsecs()
    {
        return Utils::numberFormatByLocale(Utils::PARSEC * $this->getDistAl());
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
    public function getAstrobin(): Image
    {
        return $this->astrobin;
    }

    /**
     * @param Image $astrobin
     *
     * @return DsoDTO
     */
    public function setAstrobin(Image $astrobin): DsoDTO
    {
        $this->astrobin = $astrobin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAstrobinId()
    {
        return $this->astrobinId;
    }

    /**
     * @param mixed $astrobinId
     *
     * @return DsoDTO
     */
    public function setAstrobinId($astrobinId): DsoDTO
    {
        $this->astrobinId = $astrobinId;
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
     * @return array
     */
    public function geoJson(): array
    {
        return  [
            "type" => "Feature",
            "id" => $this->getId(),
            "geometry" => $this->getGeometry(),
            "properties" => [
                "name" => $this->title(),
                "type" => $this->getType(),
                "mag" => $this->getMagnitude()
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function getDim(): ?string
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

    /**
     * @return mixed
     */
    public function getDeclinaison(): ?string
    {
        return $this->declinaison;
    }

    /**
     * @param string|null $declinaison
     *
     * @return DsoDTO
     */
    public function setDeclinaison(?string $declinaison): DsoDTO
    {
        $this->declinaison = $declinaison;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRightAscencion(): ?string
    {
        return $this->rightAscencion;
    }

    /**
     * @param mixed $rightAscencion
     *
     * @return DsoDTO
     */
    public function setRightAscencion(?string $rightAscencion): DsoDTO
    {
        $this->rightAscencion = $rightAscencion;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        $updatedAt = \DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $this->updatedAt);
        return (false !== $updatedAt) ? $updatedAt : null;
    }

    /**
     * @param mixed $updatedAt
     *
     * @return DsoDTO
     */
    public function setUpdatedAt(string $updatedAt): DsoDTO
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
