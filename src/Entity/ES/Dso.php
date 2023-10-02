<?php

declare(strict_types=1);

namespace App\Entity\ES;

/**
 * Class Dso
 * @package App\Entity
 */
class Dso
{
    private string $id;
    private string|array|null $catalog;
    private int $order;
    private string $updatedAt;
    private array|string $desigs;
    private ?array $alt = null;
    private ?array $description = null;
    private ?string $type = null;
    private ?string $constId = null;
    private ?float $mag = null;
    private ?string $dim = null;
    private ?string $cl = null;
    private ?float $distAl = null;
    private ?string $discover = null;
    private ?float $discoverYear = null;
    private ?string $ra = null;
    private ?string $dec = null;
    private ?string $astrobinId = null;
    private ?array $geometry = null;

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
     * @return array|string|null
     */
    public function getCatalog(): array|string|null
    {
        return $this->catalog;
    }

    /**
     * @param array|string|null $catalog
     *
     * @return Dso
     */
    public function setCatalog(array|string|null $catalog): Dso
    {
        $this->catalog = $catalog;
        return $this;
    }

    /**
     * @return int|null
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
    public function setOrder(int|string|null $order): Dso
    {
        $this->order = (int)$order;
        return $this;
    }

    /**
     * @return string|null
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
     * @return array|string|null
     */
    public function getDesigs(): array|string|null
    {
        return $this->desigs;
    }

    /**
     * @param array|string|null $desigs
     *
     * @return Dso
     */
    public function setDesigs(array|string|null $desigs): Dso
    {
        $this->desigs = (is_array($desigs)) ? $desigs: [$desigs];
        return $this;
    }

    /**
     * @return array|null
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
     * @return array|null
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
     * @return string|null
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
     * @return string|null
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
     * @return float|null
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
     * @return string|null
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
     * @return string|null
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
     * @return float|null
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
     * @return string|null
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
     * @return float|null
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
     * @return string|null
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
     * @return string|null
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
     * @return string|null
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
     * @return array|null
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
