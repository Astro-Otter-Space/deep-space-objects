<?php

namespace App\Entity\DTO;

use App\Classes\Utils;
use App\Entity\ES\Dso;
use AstrobinWs\Response\DTO\Item\Image;
use AstrobinWs\Response\DTO\Item\User;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

final class DsoDTO implements DTOInterface
{
    private string $id;
    private string $elasticSearchId;
    private string $relativeUrl;
    private string $absoluteUrl;
    private string $locale;
    private ?string $updatedAt = null;
    private Dso $dso;
    /**
     * @var string
     * @Groups({"search"})
     */
    private string $name;
    /**
     * @var string
     * @Groups({"search"})
     */
    private string $fullNameAlt;
    private array $catalogs;
    private ?array $catalogsLabel;
    private array|string $desigs;
    private ?string $alt = null;
    private ?string $description = null;
    /**
     * @var string
     * @Groups({"search"})
     */
    private string $type;
    /**
     * @var string
     * @Groups({"search"})
     */
    private string $typeLabel;
    private mixed $magnitude;
    private string $constellationId;
    private ?DTOInterface $constellation;
    private ?string $discover = null;
    private ?int $discoverYear = null;
    private ?string $astrobinId = null;
    private ?Image $astrobin = null;
    private ?User  $astrobinUser = null;
    private ?array $geometry = null;
    private ?string $dim = null;
    private ?string $declinaison = null;
    private ?string $rightAscencion = null;

    private ?float $distanceLightYear = 0;
    private ?int $distanceParsec = 0;
    /**
     * DsoDTO constructor.
     *
     * @param Dso $dso
     * @param string $locale
     * @param string $elasticId
     */
    public function __construct(
        Dso $dso,
        string $locale,
        string $elasticId
    )
    {
        $fieldAlt = ('en' !== $locale) ? sprintf('alt_%s', $locale) : 'alt';
        $fieldDescription = ('en' !== $locale) ? sprintf('description_%s', $locale): 'description';

        $description = $dso->getDescription()[$fieldDescription] ?? null;
        $alt = $dso->getAlt()[$fieldAlt] ?? null;

        $name = (is_array($dso->getDesigs())) ? current($dso->getDesigs()): $dso->getDesigs();
        $catalogs = (!is_array($dso->getCatalog())) ? [$dso->getCatalog()] : $dso->getCatalog();

        $dsoDesigs = $dso->getDesigs();
//        $desigs = (true === is_array($dsoDesigs)) ? array_filter($dsoDesigs, fn($d) => $d !== $dso->) : [$dsoDesigs];

        $distanceLy = Utils::numberFormatByLocale($dso->getDistAl()) ?? (int)$dso->getDistAl();
        $distancePc = Utils::numberFormatByLocale(Utils::PARSEC * (int)$this->getDistAl()) ?? (Utils::PARSEC * (int)$this->getDistAl());

        $this->setDso($dso)
            ->setLocale($locale)
            ->setElasticSearchId($elasticId)
            ->setId(strtolower($dso->getId()))
            ->setAlt($alt)
            ->setAstrobinId($dso->getAstrobinId())
            ->setConstellationId($dso->getConstId())
            ->setCatalogs($catalogs)
            ->setDesigs($dsoDesigs)
            ->setDeclinaison($dso->getDec())
            ->setDescription($description)
            ->setDim($dso->getDim())
            ->setDiscover($dso->getDiscover())
            ->setDiscoverYear($dso->getDiscoverYear())
            ->setDistAl($dso->getDistAl())
            ->setDistanceLightYear($distanceLy)
            ->setDistanceParsec($distancePc)
            ->setGeometry($dso->getGeometry())
            ->setMagnitude($dso->getMag())
            ->setName($name)
            ->setRightAscencion($dso->getRa())
            ->setType($dso->getType())
            ->setUpdatedAt($dso->getUpdatedAt())
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
     * @param mixed $locale
     *
     * @return DsoDTO
     */
    public function setLocale(mixed $locale): DsoDTO
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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return DsoDTO
     */
    public function setId(string $id): DsoDTO
    {
        $this->id = $id;
        return $this;
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
     * @return string
     */
    public function getFullNameAlt(): string
    {
        return $this->title();
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
    public function getCatalogsLabel(): array
    {
        return $this->catalogsLabel;
    }

    /**
     * @param array|null $catalogsLabel
     * @return DsoDTO
     */
    public function setCatalogsLabel(?array $catalogsLabel): DsoDTO
    {
        $this->catalogsLabel = $catalogsLabel;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getDesigs(): array|string
    {
        return $this->desigs;
    }

    /**
     * @param mixed $desigs
     *
     * @return DsoDTO
     */
    public function setDesigs(array|string $desigs): DsoDTO
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
        return $this->type;
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
     * @return string
     */
    public function getTypeLabel(): string
    {
        return $this->typeLabel;
    }

    /**
     * @param string $typeLabel
     * @return DsoDTO
     */
    public function setTypeLabel(string $typeLabel): DsoDTO
    {
        $this->typeLabel = $typeLabel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMagnitude(): mixed
    {
        return $this->magnitude;
    }

    /**
     * @param mixed $magnitude
     *
     * @return DsoDTO
     */
    public function setMagnitude(mixed $magnitude): DsoDTO
    {
        $this->magnitude = $magnitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistanceLightYear(): ?float
    {
        return $this->distanceLightYear;
    }

    /**
     * @param float|null $distanceLightYear
     * @return DsoDTO
     */
    public function setDistanceLightYear(?float $distanceLightYear): DsoDTO
    {
        $this->distanceLightYear = $distanceLightYear;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDistanceParsec(): ?int
    {
        return $this->distanceParsec;
    }

    /**
     * @param int|null $distanceParsec
     * @return DsoDTO
     */
    public function setDistanceParsec(?int $distanceParsec): DsoDTO
    {
        $this->distanceParsec = $distanceParsec;
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
     * @return DTOInterface|null
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
    private function getDistAl(): mixed
    {
        return $this->distAl;
    }

    /**
     * @param mixed $distAl
     *
     * @return DsoDTO
     */
    public function setDistAl(mixed $distAl): DsoDTO
    {
        $this->distAl = $distAl;
        return $this;
    }

    /**
     * @deprecated
     * @return bool|string|null
     */
    public function distanceLightYears(): bool|string|null
    {
        return Utils::numberFormatByLocale($this->distAl) ?? (string)$this->distAl;
    }

    /**
     * @deprecated
     * @return bool|string
     */
    public function distanceParsecs(): bool|string
    {
        return Utils::numberFormatByLocale(Utils::PARSEC * (int)$this->getDistAl()) ?? (Utils::PARSEC * (int)$this->getDistAl());
    }


    /**
     * @return string|null
     */
    public function getDiscover(): ?string
    {
        return $this->discover;
    }

    /**
     * @param mixed $discover
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
     * @return array|null
     */
    public function getGeometry(): ?array
    {
        return $this->geometry;
    }

    /**
     * @param mixed $geometry
     *
     * @return DsoDTO
     */
    public function setGeometry(array $geometry): self
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
                "type" => substr($this->getType(), strrpos($this->getType() ,'.')+1),
                "mag" => $this->getMagnitude()
            ]
        ];
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
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        if (is_null($this->updatedAt)) {
            return new DateTime('now');
        }
        $updatedAt = DateTime::createFromFormat(Utils::FORMAT_DATE_ES, $this->updatedAt);
        return (false !== $updatedAt) ? $updatedAt : (new DateTime('now'));
    }

    /**
     * @param ?string $updatedAt
     *
     * @return DsoDTO
     */
    public function setUpdatedAt(?string $updatedAt): DsoDTO
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
