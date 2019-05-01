<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Entity\Observation;
use App\Managers\DsoManager;
use App\Managers\ObservationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param ObservationManager $observationManager
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function list(ObservationManager $observationManager)
    {
        $params['geojson'] = json_encode([]);

        /** @var Response $response */
        $response = $this->render('pages/observations.html.twig', $params);
        $response->setPublic();

        return $response;
    }


    /**
     * @Route({
     *  "en": "/_observations",
     *  "fr": "/_observations",
     *  "es": "/_observations",
     *  "pt": "/_observations",
     *  "de": "/_observations"
     * }, name="observation_list_ajax")
     * @param ObservationManager $observationManager
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function geosjonAjax(ObservationManager $observationManager)
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $observationManager->getAllObservation()
        ];

        return new JsonResponse($geojson, Response::HTTP_OK);
    }

    /**
     * @Route("/observation/{name}", name="observation_show")
     *
     * @param string $name
     * @param ObservationManager $observationManager
     * @param DsoManager $dsoManager
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function show($name, ObservationManager $observationManager, DsoManager $dsoManager): Response
    {
        $params = [];

        $id = explode(trim(AbstractEntity::URL_CONCAT_GLUE), $name);
        $id = reset($id);

        /** @var Observation $observation */
        $observation = $observationManager->buildObservation($id);

        $params["observation"] = $observation;
        $params['data'] = $observationManager->formatVueData($observation);
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
