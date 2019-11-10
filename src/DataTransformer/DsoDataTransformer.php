<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\Dso;
use App\Entity\DTO\DsoDTO;
use App\Managers\DsoManager;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoDataTransformer
 * Transform an Entity Dso into DTO - Only API Use
 * @package App\DataTransformer
 */
class DsoDataTransformer
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var DsoManager */
    private $dsoManager;

    /**
     * DsoDataTransformer constructor.
     *
     * @param TranslatorInterface $translator
     * @param DsoManager $dsoManager
     */
    public function __construct(TranslatorInterface $translator, DsoManager $dsoManager)
    {
        $this->translator = $translator;
        $this->dsoManager = $dsoManager;
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
            $dsoDto->setTitle($this->dsoManager->buildTitle($dso));
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

}
