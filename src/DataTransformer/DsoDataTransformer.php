<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\DTO\ConstellationDsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\DTO\DsoDTO;
use App\Entity\ES\Constellation;
use App\Entity\ES\ListDso;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoDataTransformer
 *
 * Transform an Entity Dso into DTO - Only API Use
 * @package App\DataTransformer
 */
final class DsoDataTransformer extends AbstractDataTransformer
{
    private TranslatorInterface $translator;
    private RouterInterface $router;

    /**
     * DsoDataTransformer constructor.
     *
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router
    )
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**public function searchView(DTOInterface $dto): array
    {
        $ajaxValue = (!empty($otherDesigs)) ? sprintf('%s (%s)', $title, implode(Utils::GLUE_DASH, $otherDesigs)) : $title;
        return [
            'id' => $dto->getId(),
            'value' => $dto->title(),
            'ajaxValue' => $ajaxValue,
        ];
    }**/

    public function dsoToDto(
        DsoDTO $dsoDto,
        Constellation $constellation,
        $astrobinImage,
        $astrobinUser,
        ?array $galleryImages
    ): DTOInterface
    {
        $dso = $dsoDto->getDso();

        $fieldAlt = ('en' !== $dsoDto->getLocale()) ? sprintf('alt_%s',  $dsoDto->getLocale()) : 'alt';
        $fieldDescription = ('en' !==  $dsoDto->getLocale()) ? sprintf('description_%s',  $dsoDto->getLocale()): 'description';

        $name = (is_array($dso->getDesigs())) ? current($dso->getDesigs()): $dso->getDesigs();
        $description = $dso->getDescription()[$fieldDescription] ?? null;
        $alt = $dso->getAlt()[$fieldAlt] ?? null;
        $catalogs = (!is_array($dso->getCatalog())) ? [$dso->getCatalog()] : $dso->getCatalog();

        $distAl = Utils::numberFormatByLocale($dso->getDistAl()) ?? (string)$dso->getDistAl();
        $distPc = Utils::numberFormatByLocale(Utils::PARSEC * (int)$dso->getDistAl()) ?? (Utils::PARSEC * (int)$dso->getDistAl());

        $constellationForDso = (new ConstellationDsoDTO())
            ->setId($constellation->getId())
            ->setName('')
            ->setUrl(
                $this->router->generate('', [])
            );

        // Add data
        $dsoDto
            ->setAlt($alt)
            ->setAstrobinId($dso->getAstrobinId())
            ->setConstellationId($dso->getConstId())
            ->setCatalogs($catalogs)
            ->setDesigs($dso->getDesigs())
            ->setDeclinaison($dso->getDec())
            ->setDescription($description)
            ->setDesigs($dso->getDesigs())
            ->setDim($dso->getDim())
            ->setDiscover($dso->getDiscover())
            ->setDiscoverYear($dso->getDiscoverYear())
            ->setDistAl($distAl)
            ->setDistPc($distPc)
            ->setMagnitude($dso->getMag())
            ->setName($name)
            ->setRightAscencion($dso->getRa())
            ->setType($dso->getType())
            ->setUpdatedAt($dso->getUpdatedAt());

        // Add constellation
        $dsoDto->setConstellation($constellationForDso);

        // Set astrobin
        if (!is_nan($astrobinImage)) {
            $dsoDto->setAstrobin($astrobinImage);
            $dsoDto->setAstrobinUser($astrobinUser);
            $imgCoverAlt = ($astrobinImage->title) ? sprintf('"%s" by %s', $astrobinImage->title, $astrobinImage->user) : null;
            $dsoDto->setImgCoverAlt($imgCoverAlt);
        }

        // Add gallery
        $dsoDto->setGallery($galleryImages);

        // Add GeoJson
        $geoJson =  [
            "type" => "Feature",
            "id" => $dsoDto->getDso()->getId(),
            "geometry" => $dsoDto->getDso()->getGeometry(),
            "properties" => [
                "name" => $dsoDto->title(),
                "type" => substr($dsoDto->getType(), strrpos($dsoDto->getType() ,'.')+1),
                "mag" => $dsoDto->getMagnitude()
            ]
        ];
        $dsoDto->setGeoJson($geoJson);

        return $dsoDto;
    }

    /**
     * @todo : create classe
     * @param ListDso $listDso
     *
     * @return array
     */
    public function listVignettesView(ListDso $listDso): array
    {
        $cards = [];
        foreach ($listDso as $dsoDTO) {
            $cards[] = $this->vignetteView($dsoDTO);
        }

        return $cards;
    }


    /**
     * @param DTOInterface $dto
     *
     * @return array
     */
    public function vignetteView(DTOInterface $dto): array
    {
        $title = $dto->title() ?? $dto->getName();

        $otherDesigs = $dto->getDesigs();
        $removeDesigs = (is_array($otherDesigs))
            ? array_shift($otherDesigs)
            : null;

        $ajaxValue = (!empty($otherDesigs)) ? sprintf('%s (%s)', $title, implode(Utils::GLUE_DASH, $otherDesigs)) : $title;
        return [
            'id' => $dto->getId(),
            'value' => $title,
            'ajaxValue' => $ajaxValue,
            'subValue' => implode(Utils::GLUE_DASH, $otherDesigs),
            'label' => implode(Utils::GLUE_DASH, array_filter([$this->translator->trans($dto->getType()) , $dto->getConstellation()->title()])),
            'url' => $dto->relativeUrl(),
            'filter' => substr($dto->getType(), strrpos($dto->getType() ,'.')+1),
            'image' => $dto->getAstrobin()
        ];
    }

    /**
     * Convert Dso into Array
     *
     * @param DTOInterface $dto
     *
     * @return array|null
     */
    public function longView(DTOInterface $dto):? array
    {
        $catalogs = array_map(static function($itemCatalog) {
            return implode(Utils::DATA_GLUE, ['catalog', $itemCatalog]);
        }, $dto->getCatalogs());

        $routeConstellation = $this->router->generate('constellation_show', [
                'id' => strtolower($dto->getConstellation()->getId()), //implode(Dso::DATA_GLUE, ['constellation', strtolower($entity->getConstId())]),
                'name' => Utils::camelCaseUrlTransform($dto->getConstellation()->title())
            ]
        );

        $astrobinCredit = static function(DTOInterface $dto) {
            if (is_null($dto->getAstrobinId())) {
                return '';
            }

            $astrobinUser = $dto->getAstrobinUser();

            if (!is_null($astrobinUser)) {
                if ('' !== $astrobinUser->website) {
                    return sprintf(
                        '<a href="https://www.astrobin.com/users/%s" title="%s" target="_blank">%s</a> (<a href="%s" target="_blank">%s</a>)',
                        $astrobinUser->username,
                        $astrobinUser->username,
                        implode(Utils::DATA_CONCAT_GLUE, [$dto->getAstrobin()->title, $astrobinUser->username]),
                        $astrobinUser->website,
                        $astrobinUser->website
                    );
                }
            }

            return sprintf(
                '<a href="https://www.astrobin.com/users/%s" title="%s" target="_blank">%s</a>',
                $astrobinUser->username,
                $astrobinUser->username,
                implode(Utils::DATA_CONCAT_GLUE, [$dto->getAstrobin()->title, $astrobinUser->username]),
            );
        };

        $data = [
            'catalog' => $catalogs,
            'desigs' => implode(Utils::DATA_CONCAT_GLUE, array_filter($dto->getDesigs())),
            'type' => $dto->getType(),
            'constId' => sprintf('<a href="%s" title="%s">%s</a>', $routeConstellation, $dto->getConstellation()->title(), $dto->getConstellation()->title()),
            'mag' => $dto->getMagnitude(),
            'distAl' => $dto->distanceLightYears(),
            'distPc' => $dto->distanceParsecs(),
            'discover' => $dto->getDiscover(),
            'discoverYear' => $dto->getDiscoverYear(),
            'ra' => $dto->getRightAscencion(),
            'dec' => $dto->getDeclinaison(),
            'astrobin.credit' => $astrobinCredit($dto)
        ];

        return array_filter($data, static function($value) {
            return (false === empty($value));
        });
    }


    /**
     * Build a "table" of data (translated if needed) from DTO with translated label
     * todo : move to parent method ?
     *
     * @param DTOInterface $dto
     * @param array $listFields
     *
     * @return array
     */
    public function buildTableData(DTOInterface $dto, array $listFields): array
    {
        $dtoArray = $this->longView($dto);

        return array_map(function($value, $key) use($listFields) {
            if (!is_array($value)) {
                $valueTranslated = $this->translator->trans((string)$value, ['%count%' => 1]);
                $nbItems = 1;
            } else {
                $valueTranslated = implode(Utils::GLUE_DASH, array_map(function($item) {
                    return $this->translator->trans((string)$item, ['%count%' => 1]);
                }, $value));
                $nbItems = count($value);
            }

            return [
                'col0' => $this->translator->trans($key, ['%count%' => (string)$nbItems]),
                'col1' => (in_array($key, $listFields, true)) ? $valueTranslated: $value
            ];
        }, $dtoArray, array_keys($dtoArray));
    }
}
