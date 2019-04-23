<?php

namespace App\Controller;

use App\Entity\Observation;
use App\Managers\DsoManager;
use App\Managers\ObservationManager;
use App\Repository\ObservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Observation
 *
 * @package App\Controller
 */
class ObservationController extends AbstractController
{

    /**
     * @Route({
     *  "en": "/observations-list",
     *  "fr": "/liste-des-observations",
     *  "es": "/observations-list",
     *  "pt": "/observations-list",
     *  "de": "/observations-list"
     * }, name="observation_list")
     *
     * @return Response
     */
    public function list()
    {
        $params = [];

        /** @var Response $response */
        $response = $this->render('pages/observations.html.twig', $params);
        $response->setPublic();

        return $response;
    }

    /**
     * @Route("/observation/{id}", name="observation_show")
     *
     * @param $id
     *
     * @param ObservationManager $observationManager
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function show($id, ObservationManager $observationManager, DsoManager $dsoManager)
    {
        $params = [];

        /** @var Observation $observation */
        $observation = $observationManager->buildObservation($id);

        $params["observation"] = $observation;
        $params['list_dso'] = $dsoManager->buildListDso($observation->getDsoList());
        $params['coordinates'] = [
            'lon' => $observation->getLocation()['coordinates'][0],
            'lat' => $observation->getLocation()['coordinates'][1]
        ];

        /** @var Response $response */
        $response = $this->render('pages/observation.html.twig', $params);
        $response->setPublic();

        return $response;
    }

    /**
     *
     */
    public function add()
    {

    }

    /**
     *
     */
    public function delete()
    {

    }

}
