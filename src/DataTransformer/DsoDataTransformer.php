<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\ES\Dso;
use App\Entity\DTO\DsoDTO;
use App\Managers\DsoManager;
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

    /**
     * DsoDataTransformer constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
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
     * @param Dso|null $entity
     *
     * @return array|null
     */
    public function toArray($entity):? array
    {
        $catalog = array_map(function($itemCatalog) {
            return implode(Dso::DATA_GLUE, ['catalog', $itemCatalog]);
        }, $entity->getCatalog());

        $data = [
            'catalog' => $catalog, //implode(self::DATA_GLUE, ['catalog', $this->getCatalog()]),
            'desigs' => implode(Dso::DATA_CONCAT_GLUE, array_filter($entity->getDesigs())),
            'type' => implode(Dso::DATA_GLUE, ['type', $entity->getType()]),
            'constId' => implode(Dso::DATA_GLUE, ['constellation', strtolower($entity->getConstId())]),
            'mag' => $entity->getMag(),
            'distAl' => Utils::numberFormatByLocale($entity->getDistAl()),
            'distPc' => Utils::numberFormatByLocale(Utils::PARSEC * $entity->getDistAl()),
            'discover' => $entity->getDiscover(),
            'discoverYear' => $entity->getDiscoverYear(),
            'ra' => $entity->getRa(),
            'dec' => $entity->getDec(),
            'astrobin.credit' => sprintf('"%s" %s %s', $entity->getImage()->title, Dso::DATA_CONCAT_GLUE, $entity->getImage()->user )
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }

}
