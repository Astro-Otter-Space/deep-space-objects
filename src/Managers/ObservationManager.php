<?php

namespace App\Managers;

use App\Classes\CacheInterface;
use App\Entity\Dso;
use App\Entity\ListDso;
use App\Entity\Observation;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ObservationRepository;

/**
 * Class ObservationManager
 *
 * @package App\Managers
 */
class ObservationManager
{
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
    /**
     * ObservationManager constructor.
     *
     * @param ObservationRepository $observationRepository
     * @param UrlGenerateHelper $urlGeneratorHelper
     * @param CacheInterface $cacheUtil
     * @param $locale
     */
    public function __construct(ObservationRepository $observationRepository, UrlGenerateHelper $urlGeneratorHelper, CacheInterface $cacheUtil, $locale, DsoManager $dsoManager)
    {
        $this->observationRepository = $observationRepository;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->cacheUtil = $cacheUtil;
        $this->locale = $locale;
        $this->dsoManager = $dsoManager;
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
        $observation->setFullUrl($this->urlGeneratorHelper->generateUrl($observation));

        if (0 < count($observation->getDsoList())) {
            /** @var ListDso $dsoList */
            $dsoList = new ListDso();

            $listIdDso = array_values($observation->getDsoList());
            array_walk($listIdDso, function($id) use ($dsoList) {
                $dso = $this->dsoManager->buildDso($id);
                $dsoList->addDso($dso);
            });

            $observation->setDsoList($dsoList);
        }

        return $observation;
    }
}