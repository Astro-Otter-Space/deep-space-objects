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
    private $id;
    private $elasticSearchId;
    private string $absoluteUrl;
    private string $relativeUrl;
    private string $locale;

    private $geometry;
    private $geometryLine;
    private $image;
    private $map;
    private $generic;
    private ?string $alt = null;
    private ?string $description = null;
    private $kind;
    private Constellation $constellation;

    /**
     * ConstellationDTO constructor.
     *
     * @param Constellation $constellation
     * @param string $locale
     * @param string $elasticId
     */
    public function __construct(Constellation $constellation, string $locale, string $elasticId)
    {
        $fieldDescription = ('en' !== $locale) ? sprintf('description_%s', $locale): 'description';
        $fieldAlt = ('en' !== $locale) ?  sprintf('alt_%s', $locale): 'alt';

        $this->setConstellation($constellation)
            ->setElasticSearchId($elasticId)
            ->setLocale($locale)
            ->setId($constellation->getId())
            ->setAlt($constellation->getAlt()[$fieldAlt])
            ->setDescription($constellation->getDescription()[$fieldDescription])
            ->setGeneric($constellation->getGen())
            ->setKind($constellation->getLoc())
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
        return ucfirst($this->getAlt());
    }

    /**
     * @return string
     */
    public function relativeUrl(): string
    {
        return $this->relativeUrl;
    }

    /**
     * @return string|null
     */
    public function absoluteUrl(): ?string
    {
        return $this->absoluteUrl;
    }

    /**
     * @param string $url
     *
     * @return DTOInterface
     */
    public function setRelativeUrl(string $url): DTOInterface
    {
        $this->relativeUrl = $url;
        return $this;
    }

    /**
     * @param string $url
     *
     * @return DTOInterface
     */
    public function setAbsoluteUrl(string $url): DTOInterface
    {
        $this->absoluteUrl = $url;
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
     * @return ConstellationDTO
     */
    public function setId(string $id): ConstellationDTO
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
    public function setElasticSearchId(string $elasticSearchId): ConstellationDTO
    {
        $this->elasticSearchId = $elasticSearchId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     *
     * @return ConstellationDTO
     */
    public function setLocale(string $locale): ConstellationDTO
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
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     *
     * @return ConstellationDTO
     */
    public function setAlt(?string $alt): ConstellationDTO
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
    public function setGeometry(array $geometry): ConstellationDTO
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeometryLine(): ?array
    {
        return $this->geometryLine;
    }

    /**
     * @param mixed $geometryLine
     *
     * @return ConstellationDTO
     */
    public function setGeometryLine(array $geometryLine): ConstellationDTO
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
    public function setGeneric(?string $generic): ConstellationDTO
    {
        $this->generic = $generic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return ConstellationDTO
     */
    public function setDescription(?string $description): ConstellationDTO
    {
        $this->description = $description;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getKind(): ?string
    {
        return $this->kind;
    }

    /**
     * @param mixed $kind
     *
     * @return ConstellationDTO
     */
    public function setKind(?string $kind): ConstellationDTO
    {
        $this->kind = $kind;
        return $this;
    }

}
