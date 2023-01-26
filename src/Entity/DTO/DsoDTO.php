<?php

namespace App\Entity\DTO;

use App\Classes\Utils;
use App\Entity\ES\Dso;
use AstrobinWs\Response\Image;
use AstrobinWs\Response\User;

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
    private string $relativeUrl;
    private string $absoluteUrl;
    private string $locale;
    private ?string $updatedAt;
    private Dso $dso;

    /**
     * Data
     */
    private string $name;
    private array $catalogs;
    private array $desigs;
    private ?string $alt;
    private ?string $description;
    private string $type;
    private int $magnitude;
    private string $constellationId;
    private ConstellationDTO|DTOInterface $constellation;
    private float $distAl;
    private float $distPc;
    private ?string $discover;
    private ?int $discoverYear;
    private ?string $astrobinId;
    private Image $astrobin;
    private ?User  $astrobinUser;
    private ?string $imgCoverAlt;
    private ?string $dim;
    private ?string $declinaison;
    private ?string $rightAscencion;
    private ?array $gallery;
    private array $geoJson;
    /**
     * @var mixed|string
     */
    private string $elasticSearchId;

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
        ;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return DsoDTO
     */
    public function setLocale(string $locale): DsoDTO
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
     * @return Dso
     */
    public function getDso(): Dso
    {
        return $this->dso;
    }

    /**
     * @param mixed $elasticSearchId
     *
     * @return DsoDTO
     */
    public function setElasticSearchId(string $elasticSearchId): DsoDTO
    {
        $this->elasticSearchId = $elasticSearchId;
        return $this;
    }

    /**
     * @return string
     */
    public function guid(): string
    {
        return md5(sprintf('%s_%s', $this->getDso()->getId(), $this->locale));
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
        $title = (empty($this->getAlt()))
            ? $desig
            : implode (Utils::DATA_CONCAT_GLUE, [$this->getAlt(), $desig]);

        // If title still empty, we put Id
        return (empty($title))
            ? $this->getName()
            : $title;
    }

    /**
     * @return string
     */
    public function relativeUrl(): string
    {
        return $this->relativeUrl;
    }

    /**
     * @return string
     */
    public function absoluteUrl(): string
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return DsoDTO
     */
    public function setName(string $name): DsoDTO
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getCatalogs(): array
    {
        return $this->catalogs;
    }

    /**
     * @param array $catalogs
     *
     * @return DsoDTO
     */
    public function setCatalogs(array $catalogs): DsoDTO
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
     * @param array $desigs
     * @return $this
     */
    public function setDesigs(array $desigs): DsoDTO
    {
        $this->desigs = $desigs;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string|null $alt
     *
     * @return $this
     */
    public function setAlt(?string $alt): DsoDTO
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return DsoDTO
     */
    public function setDescription(?string $description): DsoDTO
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return sprintf('type.%s', $this->type);
    }

    /**
     * @param mixed $type
     *
     * @return DsoDTO
     */
    public function setType(string $type): DsoDTO
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMagnitude(): ?int
    {
        return $this->magnitude;
    }

    /**
     * @param int|null $magnitude
     *
     * @return DsoDTO
     */
    public function setMagnitude(?int $magnitude): DsoDTO
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
     * @return ConstellationDsoDTO|null
     */
    public function getConstellation(): ?DTOInterface
    {
        return $this->constellation;
    }

    /**
     * @param DTOInterface $constellation
     *
     * @return DsoDTO
     */
    public function setConstellation(DTOInterface $constellation): DsoDTO
    {
        $this->constellation = $constellation;
        return $this;
    }

    /**
     * @return float
     */
    private function getDistAl(): float
    {
        return $this->distAl;
    }

    /**
     * @param float $distAl
     *
     * @return DsoDTO
     */
    public function setDistAl(float $distAl): DsoDTO
    {
        $this->distAl = $distAl;
        return $this;
    }


    public function getDistPc(): float
    {
        return $this->distPc;
    }

    public function setDistPc(float $distPc): self
    {
        $this->distPc = $distPc;
        return $this;
    }


    public function distanceLightYears(): string|null
    {
        return $this->getDistAl();
    }

    public function distanceParsecs(): bool|string
    {
        return $this->getDistPc();
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
     * @return DsoDTO
     */
    public function setDiscover(?string $discover): DsoDTO
    {
        $this->discover = $discover;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDiscoverYear(): ?int
    {
        return $this->discoverYear;
    }

    /**
     * @param mixed $discoverYear
     *
     * @return DsoDTO
     */
    public function setDiscoverYear(?int $discoverYear): DsoDTO
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
     * @param Image|null $astrobin
     *
     * @return DsoDTO
     */
    public function setAstrobin(?Image $astrobin): DsoDTO
    {
        $this->astrobin = $astrobin;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getAstrobinUser(): ?User
    {
        return $this->astrobinUser;
    }

    /**
     * @param User|null $astrobinUser
     *
     * @return DsoDTO
     */
    public function setAstrobinUser(?User $astrobinUser): DsoDTO
    {
        $this->astrobinUser = $astrobinUser;
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
     * @param mixed $astrobinId
     *
     * @return DsoDTO
     */
    public function setAstrobinId(?string $astrobinId): DsoDTO
    {
        $this->astrobinId = $astrobinId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImgCoverAlt(): ?string
    {
        return $this->imgCoverAlt;
    }

    /**
     * @param string|null $imgCoverAlt
     * @return DsoDTO
     */
    public function setImgCoverAlt(?string $imgCoverAlt): DsoDTO
    {
        $this->imgCoverAlt = $imgCoverAlt;
        return $this;
    }

    /**
     * @return array
     */
    public function getGeoJson(): array
    {
        return $this->geoJson;
    }

    /**
     * @param array $geoJson
     * @return DsoDTO
     */
    public function setGeoJson(array $geoJson): self
    {
        $this->geoJson = $geoJson;
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
     * @return $this
     */
    public function setDim(?string $dim): DsoDTO
    {
        $this->dim = $dim;
        return $this;
    }

    /**
     * @return string|null
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
     * @return array
     */
    public function getGallery(): array
    {
        return $this->gallery;
    }

    /**
     * @param array|null $gallery
     * @return DsoDTO
     */
    public function setGallery(?array $gallery): self
    {
        $this->gallery = $gallery;
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
