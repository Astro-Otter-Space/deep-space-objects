<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\DTO\DTOInterface;
use App\Entity\DTO\DsoDTO;
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
    public function __construct(TranslatorInterface $translator, RouterInterface $router)
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
     * @param DsoDTO|DTOInterface $dto
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
