<?php


namespace App\Entity;

use App\Classes\Utils;
use App\Repository\DsoRepository;

/**
 * Class Dso
 * @package App\Entity
 */
class Dso extends AbstractEntity
{
    private $locale;
    private $id;
    private $elasticId;
    private $catalog;
    private $desigs;
    private $type;
    private $mag;
    private $constId;
    private $alt;
    private $dim;
    private $distAl;
    private $discover;
    private $discoverYear;
    private $ra;
    private $dec;
    private $astrobinId;
    private $image;
    private $fullUrl;
    private $geometry;

    private static $listFieldsNoMapping = ['locale', 'geometry', 'image', 'fullUrl', 'elasticId', 'order', 'data'];

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     * @return Dso
     */
    public function setLocale($locale): Dso
    {
        $this->locale = $locale;
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
     */
    public function setId($id): void
    {
        $this->id = $id;
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
     */
    public function setElasticId($elasticId): void
    {
        $this->elasticId = $elasticId;
    }

    /**
     * @return mixed
     */
    public function getCatalog()
    {
        if (empty($this->catalog)) {
            $this->catalog = [parent::UNASSIGNED];
        }

        if (!is_array($this->catalog)) {
            $this->catalog = [$this->catalog];
        }

        return $this->catalog;
    }

    /**
     * @param mixed $catalog
     */
    public function setCatalog($catalog): void
    {
        $this->catalog = $catalog;
    }

    /**
     * @return mixed
     */
    public function getDesigs()
    {
        if (!is_array($this->desigs)) {
            $this->desigs = [$this->desigs];
        }
        return $this->desigs;
    }

    /**
     * @param mixed $desigs
     */
    public function setDesigs($desigs): void
    {
        $this->desigs = $desigs;
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
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMag()
    {
        return $this->mag;
    }

    /**
     * @param mixed $mag
     */
    public function setMag($mag): void
    {
        $this->mag = $mag;
    }

    /**
     * @return mixed
     */
    public function getConstId()
    {
        return $this->constId;
    }

    /**
     * @param mixed $constId
     */
    public function setConstId($constId): void
    {
        $this->constId = $constId;
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
     */
    public function setAlt($alt): void
    {
        if (!$this->locale || 'en' === $this->locale) {
            $this->alt = $alt['alt'];
        } else {
            $this->alt = $alt[sprintf('alt_%s', $this->locale)];
        }
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
     */
    public function setDim($dim): void
    {
        $this->dim = $dim;
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
     */
    public function setDistAl($distAl): void
    {
        $this->distAl = $distAl;
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
     */
    public function setDiscover($discover): void
    {
        $this->discover = $discover;
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
     */
    public function setDiscoverYear($discoverYear): void
    {
        $this->discoverYear = $discoverYear;
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
     */
    public function setRa($ra): void
    {
        $this->ra = $ra;
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
     */
    public function setDec($dec): void
    {
        $this->dec = $dec;
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
     */
    public function setAstrobinId($astrobinId): void
    {
        $this->astrobinId = $astrobinId;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }


    /**
     * @return string
     */
    public static function getIndex()
    {
        return DsoRepository::INDEX_NAME;
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
     */
    public function setFullUrl($fullUrl): void
    {
        $this->fullUrl = $fullUrl;
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
     */
    public function setGeometry($geometry): void
    {
        $this->geometry = $geometry;
    }

    /**
     * Serialize data
     * @return array
     */
    public function toArray()
    {
        $catalog = array_map(function($itemCatalog) {
            return implode(self::DATA_GLUE, ['catalog', $itemCatalog]);
        }, $this->getCatalog());

        $data = [
            'catalog' => $catalog, //implode(self::DATA_GLUE, ['catalog', $this->getCatalog()]),
            'desigs' => implode(self::DATA_CONCAT_GLUE, array_filter($this->getDesigs())),
            'type' => implode(self::DATA_GLUE, ['type', $this->getType()]),
            'constId' => implode(self::DATA_GLUE, ['constellation', strtolower($this->getConstId())]),
            'mag' => $this->getMag(),
            'distAl' => Utils::numberFormatByLocale($this->getDistAl()),
            'distPc' => Utils::numberFormatByLocale(Utils::PARSEC * $this->getDistAl()),
            'discover' => $this->getDiscover(),
            'discoverYear' => $this->getDiscoverYear(),
            'ra' => $this->getRa(),
            'dec' => $this->getDec(),
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }

    /**
     * @return array
     */
    public function getListFieldsNoMapping()
    {
        return self::$listFieldsNoMapping;
    }
}
