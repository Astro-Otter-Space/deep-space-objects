<?php

namespace App\Managers;

use App\Classes\Utils;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\ES\Constellation;
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
        /** @var ConstellationDTO $constellation */
        $constellation = $this->constellationRepository->setlocale($this->locale)->getObjectById($id, true);
        $constellation->setFullUrl($this->urlGeneratorHelper->generateUrl($constellation));

        return $constellation;
    }


    /**
     * Get all constellation and build formated data for template
     */
    public function buildListConstellation(): array
    {
        /** @return \Generator
         * @throws \ReflectionException
         * @var \Generator $listConstellation
         */
        $listConstellation = function() {
            yield from $this->constellationRepository->setLocale($this->locale)->getAllConstellation();
        };

        dump($listConstellation()->current()->getImage()); die();

        return array_map(function(Constellation $constellation) {
            /** @var Image $image */
            $image = new Image();
            $image->url_regular = $constellation->getImage();
            $image->user = $constellation->getAlt();
            $image->title = $constellation->getAlt();

            return [
                'id' => $constellation->getId(),
                'value' => $constellation->getAlt(),
                'label' => $constellation->getGen(),
                'url' => $this->buildUrl($constellation, RouterInterface::RELATIVE_PATH),
                'image' => $image,
                'filter' => $constellation->getLoc()
            ];
        }, iterator_to_array($listConstellation()));
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
