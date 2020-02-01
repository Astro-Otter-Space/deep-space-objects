<?php

namespace App\Managers;

use App\Classes\Utils;
use App\Entity\ES\Constellation;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConstellationManager
 * @package App\Managers
 */
class ConstellationManager
{
    /** @var ConstellationRepository  */
    private $constellationRepository;
    /** @var UrlGenerateHelper  */
    private $urlGeneratorHelper;
    /** @var TranslatorInterface  */
    private $translatorInterface;
    /** @var string */
    private $locale;

    /**
     * ConstellationManager constructor.
     * @param $constellationRepository
     * @param $urlGeneratorHelper
     * @param $translatorInterface
     */
    public function __construct(ConstellationRepository $constellationRepository, UrlGenerateHelper $urlGeneratorHelper, TranslatorInterface $translatorInterface, $locale)
    {
        $this->constellationRepository = $constellationRepository;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->translatorInterface = $translatorInterface;
        $this->locale = $locale;
    }


    /**
     * Build a constellation entoty from ElasticSearch request by $id
     * @param $id
     * @return Constellation
     * @throws \ReflectionException
     */
    public function buildConstellation($id): Constellation
    {
        /** @var Constellation $constellation */
        $constellation = $this->constellationRepository->setlocale($this->locale)->getObjectById($id);
        $constellation->setFullUrl($this->urlGeneratorHelper->generateUrl($constellation));

        return $constellation;
    }


    /**
     * Get all constellation and build formated data for template
     * @throws \ReflectionException
     */
    public function buildListConstellation()
    {
        $listConstellation = $this->constellationRepository->setLocale($this->locale)->getAllConstellation();

        return array_map(function(Constellation $constellation) {
            return [
                'id' => $constellation->getId(),
                'value' => $constellation->getAlt(),
                'label' => $constellation->getGen(),
                'url' => $this->buildUrl($constellation),
                'image' => $constellation->getImage(),
                'filter' => $constellation->getLoc()
            ];
        }, iterator_to_array($listConstellation->getIterator()));
    }


    /**
     * Build search by terms
     * @param $searchTerms
     * @return mixed
     */
    public function searchConstellationsByTerms($searchTerms)
    {
        $resultConstellation = $this->constellationRepository->setLocale($this->locale)->getConstellationsBySearchTerms($searchTerms);

        return call_user_func("array_merge", array_map(function(Constellation $constellation) {
            return $this->buildSearchListConst($constellation);
        }, $resultConstellation));
    }


    /**
     * Format data constellation for Ajax research
     * @param Constellation $constellation
     * @return array
     */
    public function buildSearchListConst(Constellation $constellation)
    {
        $constellationName = $constellation->getAlt();
        return [
            'id' => $constellation->getId(),
            'value' => $constellationName,
            'ajaxValue' => $constellationName,
            'label' => implode(Utils::GLUE_DASH, [$this->translatorInterface->trans('const_id', ['%count%' => 1]), $constellation->getGen()]),
            'url' => $this->buildUrl($constellation),
        ];
    }

    /**
     * @param $constellation
     * @return string
     */
    private function buildUrl($constellation)
    {
        return $this->urlGeneratorHelper->generateUrl($constellation);
    }
}
