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
    /** @var ObservationManager  */
    private $observationManager;
    /** @var DsoManager  */
    private $dsoManager;

    /**
     * ObservationController constructor.
     *
     * @param ObservationManager $observationManager
     * @param DsoManager $dsoManager
     */
    public function __construct(ObservationManager $observationManager, DsoManager $dsoManager)
    {
        $this->observationManager = $observationManager;
        $this->dsoManager = $dsoManager;
    }

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
     * @throws \ReflectionException
     */
    public function list()
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
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function geosjonAjax()
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $this->observationManager->getAllObservation()
        ];

        return new JsonResponse($geojson, Response::HTTP_OK);
    }

    /**
     * @Route("/observation/{name}", name="observation_show")
     *
     * @param string $name
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function show($name): Response
    {
        $params = [];

        $id = explode(trim(AbstractEntity::URL_CONCAT_GLUE), $name);
        $id = reset($id);

        /** @var Observation $observation */
        $observation = $this->observationManager->buildObservation($id);

        $params["observation"] = $observation;
        $params['data'] = $this->observationManager->formatVueData($observation);
        $params['list_dso'] = $this->dsoManager->buildListDso($observation->getDsoList());
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
