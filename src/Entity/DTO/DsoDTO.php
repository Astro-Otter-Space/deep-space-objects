<?php

namespace App\Entity\DTO;

/**
 * Class DsoDTO
 *
 * @package App\Entity\DTO
 */
final class DsoDTO
{

    /** @var  */
    private $id;
    /** @var  */
    private $title;
    /** @var  */
    private $desigs;
    /** @var  */
    private $constellation;
    /** @var  */
    private $type;
    /** @var  */
    private $catalog;


    /** @var  */
    private $magnitude;

    /** @var  */
    private $dim;
    /** @var  */
    private $distAl;
    /** @var  */
    private $distPC;
    /** @var  */
    private $discover;
    /** @var  */
    private $discoverYear;
    /** @var  */
    private $ra;
    /** @var  */
    private $dec;

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
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return DsoDTO
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDesigs()
    {
        return $this->desigs;
    }

    /**
     * @param mixed $desigs
     *
     * @return DsoDTO
     */
    public function setDesigs($desigs)
    {
        $this->desigs = $desigs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * @param mixed $catalog
     *
     * @return DsoDTO
     */
    public function setCatalog($catalog): self
    {
        $this->catalog = $catalog;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return DsoDTO
     */
    public function setType(?string $type): self
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
    public function setMagnitude($magnitude): self
    {
        $this->magnitude = $magnitude;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConstellation()
    {
        return $this->constellation;
    }

    /**
     * @param mixed $constellation
     *
     * @return DsoDTO
     */
    public function setConstellation($constellation): self
    {
        $this->constellation = $constellation;
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
    public function setAlt($alt): self
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDim()
    {
        return $this->dim;
    }

    /**
     * @param mixed $dim
     *
     * @return DsoDTO
     */
    public function setDim($dim): self
    {
        $this->dim = $dim;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistAl()
    {
        return $this->distAl;
    }

    /**
     * @param mixed $distAl
     *
     * @return DsoDTO
     */
    public function setDistAl($distAl): self
    {
        $this->distAl = $distAl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDistPC()
    {
        return $this->distPC;
    }

    /**
     * @param mixed $distPC
     *
     * @return DsoDTO
     */
    public function setDistPC($distPC)
    {
        $this->distPC = $distPC;
        return $this;
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
    public function setDiscover($discover): self
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
    public function setDiscoverYear($discoverYear): self
    {
        $this->discoverYear = $discoverYear;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRa()
    {
        return $this->ra;
    }

    /**
     * @param mixed $ra
     *
     * @return DsoDTO
     */
    public function setRa($ra): self
    {
        $this->ra = $ra;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDec()
    {
        return $this->dec;
    }

    /**
     * @param mixed $dec
     *
     * @return DsoDTO
     */
    public function setDec($dec): self
    {
        $this->dec = $dec;
        return $this;
    }



}
