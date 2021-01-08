<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Entity\ES\Constellation;
use App\Repository\ConstellationRepository;

/**
 * Class ConstellationDTO
 * @package Entity\DTO
 */
final class ConstellationDTO implements DTOInterface
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

    /** @var Constellation */
    private $constellation;

    private $geometry;
    private $geometryLine;
    private $image;
    private $map;
    private $generic;
    private $alt;
    private $description;

    /**
     * ConstellationDTO constructor.
     *
     * @param Constellation $constellation
     * @param string $locale
     * @param string $elasticId
     */
    public function __construct(Constellation $constellation, string $locale, string $elasticId)
    {
        $this->setConstellation($constellation)
            ->setElasticSearchId($elasticId)
            ->setLocale($locale)
            ->setId($constellation->getId())
            ->setAlt($constellation->getAlt())
            ->setDescription($constellation->getDescription())
            ->setGeneric($constellation->getGen())
            ->setGeometry($constellation->getGeometry())
            ->setGeometryLine($constellation->getGeometryLine());
        ;
    }


    /**
     * @return string
     */
    public function guid(): string
    {
        return md5(sprintf('%s_%s', $this->getId(), $this->locale));
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $field = ('en' !== $this->locale)? sprintf('alt_%s',$this->locale): 'alt';
        return ucfirst($this->getAlt()[$field]);
    }


    public function fullUrl(): string
    {
        // TODO: Implement fullUrl() method.
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
     * @return ConstellationDTO
     */
    public function setId($id): ConstellationDTO
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
     * @return ConstellationDTO
     */
    public function setElasticSearchId($elasticSearchId): ConstellationDTO
    {
        $this->elasticSearchId = $elasticSearchId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullUrl(): string
    {
        return $this->fullUrl;
    }

    /**
     * @param mixed $fullUrl
     *
     * @return ConstellationDTO
     */
    public function setFullUrl($fullUrl): ConstellationDTO
    {
        $this->fullUrl = $fullUrl;
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
     * @return ConstellationDTO
     */
    public function setLocale($locale): ConstellationDTO
    {
        $this->locale = $locale;
        return $this;
    }


    /**
     * @param Constellation $constellation
     *
     * @return ConstellationDTO
     */
    public function setConstellation(Constellation $constellation): ConstellationDTO
    {
        $this->constellation = $constellation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlt(): ?array
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     *
     * @return ConstellationDTO
     */
    public function setAlt(array $alt): ConstellationDTO
    {
        $this->alt = $alt;
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
     * @return ConstellationDTO
     */
    public function setGeometry($geometry): ConstellationDTO
    {
        $this->geometry = $geometry;
        return $this;
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
     *
     * @return ConstellationDTO
     */
    public function setGeometryLine($geometryLine): ConstellationDTO
    {
        $this->geometryLine = $geometryLine;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage(): string
    {
        return sprintf(ConstellationRepository::URL_IMG, strtolower($this->getId()));
    }


    /**
     * @return mixed
     */
    public function getMap(): string
    {
        return sprintf(ConstellationRepository::URL_MAP, strtoupper($this->getId()));
    }

    /**
     * @return mixed
     */
    public function getGeneric()
    {
        return $this->generic;
    }

    /**
     * @param mixed $generic
     *
     * @return ConstellationDTO
     */
    public function setGeneric($generic): ConstellationDTO
    {
        $this->generic = $generic;
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
     * @return ConstellationDTO
     */
    public function setDescription($description): ConstellationDTO
    {
        $this->description = $description;
        return $this;
    }


    public function geoJson(): array
    {
        return [

        ];
    }

}
