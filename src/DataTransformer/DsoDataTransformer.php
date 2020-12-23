<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Dso;
use App\Entity\DTO\DsoDTO;
use App\Managers\DsoManager;
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
    /** @var TranslatorInterface */
    private $translator;

    /** @var RouterInterface */
    private $router;

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

    /**
     * @deprecated
     * @param $dso
     *
     * @return DsoDTO|null
     */
    public function transform($dso):? DsoDTO
    {
        if ($dso instanceof Dso) {
            /** @var DsoDTO $dsoDto */
            $dsoDto = new DsoDTO();

            $dsoDto->setId($dso->getId());
            $dsoDto->setTitle(DsoManager::buildTitleStatic($dso));
            $dsoDto->setConstellation($this->translator->trans(sprintf('constellation.%s', strtolower($dso->getConstId()))));
            $dsoDto->setDesigs($dso->getDesigs());
            $dsoDto->setType($this->translator->trans(sprintf('type.%s', $dso->getType())));
            $dsoDto->setCatalog($dso->getCatalog());

            $dsoDto->setMagnitude($dso->getMag());
            $dsoDto->setDistAl(Utils::numberFormatByLocale($dso->getDistAl()));
            $dsoDto->setDistPC(Utils::numberFormatByLocale(Utils::PARSEC * $dso->getDistAl()));

            $dsoDto->setDiscover($dso->getDiscover());
            $dsoDto->setDiscoverYear($dso->getDiscoverYear());
            $dsoDto->setDec($dso->getDec());
            $dsoDto->setRa($dso->getRa());

            return $dsoDto;
        }

        return null;
    }

    /**
     * Convert Dso into Array
     *
     * @param DTOInterface $dto
     *
     * @return array|null
     */
    public function toArray(DTOInterface $dto):? array
    {
        $catalog = array_map(static function($itemCatalog) {
            return implode(Utils::DATA_GLUE, ['catalog', $itemCatalog]);
        }, $dto->getCatalogs());

        $constellation = $this->translator->trans(implode(Utils::DATA_GLUE, ['constellation', strtolower($dto->getConstellation()->title())]));
        $routeConstellation = $this->router->generate('constellation_show', [
                'id' => strtolower($dto->getConstellation()->getId()), //implode(Dso::DATA_GLUE, ['constellation', strtolower($entity->getConstId())]),
                'name' => Utils::camelCaseUrlTransform($constellation)
            ]
        );

        $data = [
            'catalog' => $catalog,
            'desigs' => implode(Utils::DATA_CONCAT_GLUE, array_filter($dto->getDesigs())),
            'type' => implode(Utils::DATA_GLUE, ['type', $dto->getType()]),
            'constId' => sprintf('<a href="%s" title="%s">%s</a>', $routeConstellation, $constellation, $constellation),
            'mag' => $dto->getMagnitude(),
            'distAl' => $dto->getDistAl(),
            'distPc' => $dto->getDistPc(),
            'discover' => $dto->getDiscover(),
            'discoverYear' => $dto->getDiscoverYear(),
            'ra' => $dto->getRightAscencion(),
            'dec' => $dto->getDeclinaison(),
            'astrobin.credit' => (!is_null($dto->getAstrobinId())) ? sprintf('"%s" %s %s', $dto->getAstrobinImage()->title, Utils::DATA_CONCAT_GLUE, $entity->getImage()->user ) : ''
        ];

        return array_filter($data, static function($value) {
            return (false === empty($value));
        });
    }

}
