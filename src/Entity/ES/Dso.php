<?php

declare(strict_types=1);

namespace App\Entity\ES;

/**
 * Class Dso
 * @package App\Entity
 */
class Dso
{
    /** @var string */
    private $id;

    /** @var array|string */
    private $catalog;

    /** @var int */
    private $order;

    /** @var string */
    private $updatedAt;

    /** @var array */
    private $desigs;

    /** @var array */
    private $alt;

    /** @var array */
    private $description;

    /** @var string */
    private $type;

    /** @var string */
    private $constId;

    /** @var float */
    private $mag;

    /** @var string */
    private $dim;

    /** @var string */
    private $cl;

    /** @var float */
    private $distAl;

    /** @var string */
    private $discover;

    /** @var float */
    private $discoverYear;

    /** @var string */
    private $ra;

    /** @var string */
    private $dec;

    /** @var string */
    private $astrobinId;

    /** @var array */
    private $geometry;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Dso
     */
    public function setId(string $id): Dso
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * @param array|string|null $catalog
     *
     * @return Dso
     */
    public function setCatalog($catalog): Dso
    {
        $this->catalog = $catalog;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): ?int
    {
        return $this->order;
    }

    /**
     * @param int|string|null $order
     *
     * @return Dso
     */
    public function setOrder($order): Dso
    {
        $this->order = (int)$order;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return Dso
     */
    public function setUpdatedAt(?string $updatedAt): Dso
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return array
     */
    public function getDesigs(): ?array
    {
        return $this->desigs;
    }

    /**
     * @param array|null $desigs
     *
     * @return Dso
     */
    public function setDesigs($desigs): Dso
    {
        $this->desigs = (is_array($desigs)) ? $desigs: [$desigs];
        return $this;
    }

    /**
     * @return array
     */
    public function getAlt(): ?array
    {
        return $this->alt;
    }

    /**
     * @param array|null $alt
     *
     * @return Dso
     */
    public function setAlt(?array $alt): Dso
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * @return array
     */
    public function getDescription(): ?array
    {
        return $this->description;
    }

    /**
     * @param array|null $description
     *
     * @return Dso
     */
    public function setDescription(?array $description): Dso
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return Dso
     */
    public function setType(?string $type): Dso
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getConstId(): ?string
    {
        return $this->constId;
    }

    /**
     * @param string|null $constId
     *
     * @return Dso
     */
    public function setConstId(?string $constId): Dso
    {
        $this->constId = $constId;
        return $this;
    }

    /**
     * @return float
     */
    public function getMag(): ?float
    {
        return $this->mag;
    }

    /**
     * @param float|null $mag
     *
     * @return Dso
     */
    public function setMag(?float $mag): Dso
    {
        $this->mag = $mag;
        return $this;
    }

    /**
     * @return string
     */
    public function getDim(): ?string
    {
        return $this->dim;
    }

    /**
     * @param string|null $dim
     *
     * @return Dso
     */
    public function setDim(?string $dim): Dso
    {
        $this->dim = $dim;
        return $this;
    }

    /**
     * @return string
     */
    public function getCl(): ?string
    {
        return $this->cl;
    }

    /**
     * @param string|null $cl
     *
     * @return Dso
     */
    public function setCl(?string $cl): Dso
    {
        $this->cl = $cl;
        return $this;
    }

    /**
     * @return float
     */
    public function getDistAl(): ?float
    {
        return $this->distAl;
    }

    /**
     * @param float|null $distAl
     *
     * @return Dso
     */
    public function setDistAl(?float $distAl): Dso
    {
        $this->distAl = $distAl;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscover(): ?string
    {
        return $this->discover;
    }

    /**
     * @param string|null $discover
     *
     * @return Dso
     */
    public function setDiscover(?string $discover): Dso
    {
        $this->discover = $discover;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscoverYear(): ?float
    {
        return $this->discoverYear;
    }

    /**
     * @param float|null $discoverYear
     *
     * @return Dso
     */
    public function setDiscoverYear(?float $discoverYear): Dso
    {
        $this->discoverYear = $discoverYear;
        return $this;
    }

    /**
     * @return string
     */
    public function getRa(): ?string
    {
        return $this->ra;
    }

    /**
     * @param string|null $ra
     *
     * @return Dso
     */
    public function setRa(?string $ra): Dso
    {
        $this->ra = $ra;
        return $this;
    }

    /**
     * @return string
     */
    public function getDec(): ?string
    {
        return $this->dec;
    }

    /**
     * @param string|null $dec
     *
     * @return Dso
     */
    public function setDec(?string $dec): Dso
    {
        $this->dec = $dec;
        return $this;
    }

    /**
     * @return string
     */
    public function getAstrobinId(): ?string
    {
        return $this->astrobinId;
    }

    /**
     * @param string|null $astrobinId
     *
     * @return Dso
     */
    public function setAstrobinId(?string $astrobinId): Dso
    {
        $this->astrobinId = $astrobinId;
        return $this;
    }

    /**
     * @return array
     */
    public function getGeometry(): ?array
    {
        return $this->geometry;
    }

    /**
     * @param array|null $geometry
     *
     * @return Dso
     */
    public function setGeometry(?array $geometry): Dso
    {
        $this->geometry = $geometry;
        return $this;
    }
}
