<?php

namespace App\Managers;

use App\Classes\Utils;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\ES\Constellation;
use App\Entity\ES\ListConstellation;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use AstrobinWs\Response\Image;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
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
    private $translator;
    /** @var string */
    private $locale;

    /**
     * ConstellationManager constructor.
     *
     * @param ConstellationRepository $constellationRepository
     * @param UrlGenerateHelper $urlGeneratorHelper
     * @param TranslatorInterface $translator
     * @param string $locale
     */
    public function __construct(ConstellationRepository $constellationRepository, UrlGenerateHelper $urlGeneratorHelper, TranslatorInterface $translator, string $locale)
    {
        $this->constellationRepository = $constellationRepository;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->translator = $translator;
        $this->locale = $locale;
    }


    /**
     * Build a constellation entoty from ElasticSearch request by $id
     *
     * @param $id
     *
     * @return ConstellationDTO
     * @throws \ReflectionException
     */
    public function buildConstellation($id): ConstellationDTO
    {
        /** @var ConstellationDTO $constellation */
        $constellation = $this->constellationRepository
            ->setlocale($this->locale)
            ->getObjectById($id, true);

        return $constellation;
    }


    /**
     * Get all constellation and build formated data for template
     */
    public function buildListConstellation(): ListConstellation
    {
        /** @return \Generator
         * @throws \ReflectionException
         * @var \Generator $listConstellation
         */
        $getConstellation = function() {
            yield from $this->constellationRepository->setLocale($this->locale)->getAllConstellation();
        };

        $listConstellation = new ListConstellation();
        foreach ($getConstellation() as $constellation) {
            $listConstellation->addConstellation($constellation);
        }

        return $listConstellation;
    }


    /**
     * Build search by terms
     *
     * @param $searchTerms
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function searchConstellationsByTerms($searchTerms)
    {
        $resultConstellation = $this->constellationRepository->setLocale($this->locale)->getConstellationsBySearchTerms($searchTerms);

        return array_merge(array_map(function (Constellation $constellation) {
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
            'label' => implode(Utils::GLUE_DASH, [$this->translator->trans('const_id', ['%count%' => 1]), $constellation->getGen()]),
            'url' => $this->buildUrl($constellation, Router::ABSOLUTE_PATH),
        ];
    }

    /**
     * @param Constellation $constellation
     * @param string $typeUrl
     *
     * @return string
     */
    private function buildUrl(Constellation $constellation, string $typeUrl)
    {
        return $this->urlGeneratorHelper->generateUrl($constellation, $typeUrl, $this->locale);
    }
}
