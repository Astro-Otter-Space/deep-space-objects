<?php

namespace App\Managers;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\Entity\ListDso;
use App\Entity\Observation;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ObservationRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ObservationManager
 *
 * @package App\Managers
 */
class ObservationManager
{
    use ManagerTrait;

    /** @var ObservationRepository  */
    private $observationRepository;
    /** @var UrlGenerateHelper  */
    private $urlGeneratorHelper;
    /** @var CacheInterface  */
    private $cacheUtil;
    /** @var  */
    private $locale;
    /** @var  */
    private $dsoManager;
    /** @var TranslatorInterface */
    private $translatorInterface;

    /**
     * ObservationManager constructor.
     *
     * @param ObservationRepository $observationRepository
     * @param UrlGenerateHelper $urlGeneratorHelper
     * @param CacheInterface $cacheUtil
     * @param $locale
     * @param DsoManager $dsoManager
     * @param TranslatorInterface $translatorInterface
     */
    public function __construct(ObservationRepository $observationRepository, UrlGenerateHelper $urlGeneratorHelper, CacheInterface $cacheUtil, $locale, DsoManager $dsoManager, TranslatorInterface $translatorInterface)
    {
        $this->observationRepository = $observationRepository;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->cacheUtil = $cacheUtil;
        $this->locale = $locale;
        $this->dsoManager = $dsoManager;
        $this->translatorInterface = $translatorInterface;
    }

    /**
     * @param $id
     *
     * @return Observation
     * @throws \ReflectionException
     */
    public function buildObservation($id): Observation
    {
        /** @var Observation $observation */
        $observation = $this->observationRepository->setLocale($this->locale)->getObservationById($id);
        if ($observation instanceof Observation) {
            $observation->setFullUrl($this->urlGeneratorHelper->generateUrl($observation));
            /** @var ListDso $dsoList */
            $dsoList = new ListDso();

            if (!is_null($observation->getDsoList()) && 0 < count($observation->getDsoList())) {

                $listIdDso = array_values($observation->getDsoList());
                array_walk($listIdDso, function($id) use ($dsoList) {
                    $dso = $this->dsoManager->buildDso($id);
                    $dsoList->addDso($dso);
//                    $observation->getDsoList()->addDso($dso);
                });
            }

            $observation->setDsoList($dsoList);
        }

        return $observation;
    }

    /**
     * @param Observation $observation
     *
     * @return array
     */
    public function formatVueData(Observation $observation)
    {
        return $this->formatEntityData($observation, [], $this->translatorInterface);
    }


    /***
     * Format result search observation
     * @param $terms
     *
     * @return mixed
     */
    public function buildSearchObservationByTerms($terms)
    {
        $listObservation = $this->observationRepository->setLocale($this->locale)->getObservationsBySearchTerms($terms);
        return call_user_func("array_merge", array_map(function (Observation $observation) {
            return [
                'id' => $observation->getId(),
                'value' => $observation->getName(),
                'label' => $observation->getUsername(),
                'url' => $this->urlGeneratorHelper->generateUrl($observation)
            ];
        }, $listObservation));
    }


    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getAllObservation()
    {
        /** @var UrlGenerateHelper $urlGenerator */
        $urlGenerator = $this->urlGeneratorHelper;
        return array_map(function(Observation $observation) use($urlGenerator) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'name' => $observation->getName(),
                    'full_url' => $urlGenerator->generateUrl($observation),
                    'date' => $observation->getObservationDate(),
                    'username' => $observation->getUsername()
                ],
                'geometry' => $observation->getLocation()
            ];
        }, iterator_to_array($this->observationRepository->getAllObservation()));
    }

}