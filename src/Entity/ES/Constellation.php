<?php

namespace App\Entity\ES;


/**
 * Class Constellation
 * @package App\Entity
 */
class Constellation
{
    /** @var string */
    private $id;
    /** @var array */
    private $geometry;
    /** @var array */
    private $geometryLine;
    /** @var string */
    private $gen;
    /** @var array */
    private $alt;
    /** @var array */
    private $description;
    /** @var float */
    private $rank;
    /** @var float */
    private $order;
    /** @var string */
    private $loc;

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
     * @return Constellation
     */
    public function setId(string $id): Constellation
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function getGeometry(): array
    {
        return $this->geometry;
    }

    /**
     * @param array $geometry
     *
     * @return Constellation
     */
    public function setGeometry(array $geometry): Constellation
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * @return array
     */
    public function getGeometryLine(): array
    {
        return $this->geometryLine;
    }

    /**
     * @param array $geometryLine
     *
     * @return Constellation
     */
    public function setGeometryLine(array $geometryLine): Constellation
    {
        $this->geometryLine = $geometryLine;
        return $this;
    }

    /**
     * @return string
     */
    public function getGen(): string
    {
        return $this->gen;
    }

    /**
     * @param string $gen
     *
     * @return Constellation
     */
    public function setGen(string $gen): Constellation
    {
        $this->gen = $gen;
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
     * @return Constellation
     */
    public function setAlt(?array $alt): Constellation
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
     * @return Constellation
     */
    public function setDescription(?array $description): Constellation
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float
     */
    public function getRank(): float
    {
        return $this->rank;
    }

    /**
     * @param float $rank
     *
     * @return Constellation
     */
    public function setRank(float $rank): Constellation
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * @return float
     */
    public function getOrder(): float
    {
        return $this->order;
    }

    /**
     * @param float $order
     *
     * @return Constellation
     */
    public function setOrder(float $order): Constellation
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }

    /**
     * @param string $loc
     *
     * @return Constellation
     */
    public function setLoc(string $loc): Constellation
    {
        $this->loc = $loc;
        return $this;
    }
}
