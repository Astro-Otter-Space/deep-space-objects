<?php

namespace App\Controller;

use App\DataTransformer\ConstellationDataTransformer;
use App\DataTransformer\DsoDataTransformer;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Managers\EventManager;
use App\Managers\ObservationManager;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SearchController
 * @package App\Controller
 */
class  SearchController extends AbstractController
{
    /** @var DsoManager  */
    private $dsoManager;

    /** @var ConstellationManager  */
    private $constellationManager;

    /** @var ObservationManager  */
    private $observationManager;

    /** @var EventManager */
    private $eventManager;

    /**
     * SearchController constructor.
     *
     * @param DsoManager $dsoManager
     * @param ConstellationManager $constellationManager
     * @param ObservationManager $observationManager
     * @param EventManager $eventManager
     */
    public function __construct(DsoManager $dsoManager, ConstellationManager $constellationManager, ObservationManager $observationManager, EventManager $eventManager)
    {
        $this->dsoManager = $dsoManager;
        $this->constellationManager = $constellationManager;
        $this->observationManager = $observationManager;
        $this->eventManager = $eventManager;
    }

    /**
     * @Route(
     *     "/_search",
     *     options={"expose"=true},
     *     name="search_ajax"
     * )
     *
     * @param Request $request
     * @param DsoDataTransformer $dsoDataTransformer
     * @param ConstellationDataTransformer $constellationDataTransformer
     *
     * @return JsonResponse
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function searchAjax(Request $request, DsoDataTransformer $dsoDataTransformer, ConstellationDataTransformer $constellationDataTransformer): JsonResponse
    {
        $data = [];
        if ($request->query->has('q')) {
            $searchTerm = strtolower(filter_var($request->query->get('q'), FILTER_SANITIZE_STRING));
            $listDso = $this->dsoManager->searchDsoByTerms($searchTerm, null);
            $dataDso = $dsoDataTransformer->listVignettesView($listDso);

            $listConstellation = $this->constellationManager->searchConstellationsByTerms($searchTerm);
            $dataConstellation = $constellationDataTransformer->listVignettesView($listConstellation);
            $data = array_merge($dataDso, $dataConstellation);
        }

        /** @var JsonResponse $response */
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }

    /**
     * @Route(
     *     "/_search_dso_observation",
     *     options={"expose"=true},
     *     name="search_dso_observation"
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse $data
     */
    public function searchDsoForObservation(Request $request): JsonResponse
    {
        $data = [];
        if ($request->query->has('q')) {
            $searchTerm = strtolower(filter_var($request->query->get('q'), FILTER_SANITIZE_STRING));
            $data = $this->dsoManager->searchDsoByTerms($searchTerm, 'id');
        }

        /** @var JsonResponse $response */
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        return $response;
    }

    /**
     * Search events and dso planner by autocomplete
     *
     * @Route(
     *     "/_search-observation",
     *     name="search_observation_ajax",
     *     options={"exposes"=true}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function searchObservationAjax(Request $request): JsonResponse
    {
        $data = [];
        if ($request->query->has('q')) {
            $searchTerm = strtolower(filter_var($request->query->get('q'), FILTER_SANITIZE_STRING));
            $data = array_merge(
                $this->observationManager->buildSearchObservationByTerms($searchTerm),
                $this->eventManager->buildSearchEventByTerms($searchTerm)
            );
        }

        /** @var JsonResponse $response */
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        return $response;
    }

    /**
     * @Route(
     *     "/build/data/stars.{id}.json",
     *     options={"expose"=true},
     *     name="list_stars"
     * )
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function starsFiltered(Request $request, string $id): JsonResponse
    {
        $webPath = $this->getParameter('kernel.project_dir') . '/public/';
        $file = $webPath . 'build/data/stars.8.json';

        $starsData = [
            "type"  => "FeatureCollection",
            "features" => []
        ];

        if (file_exists($file)) {

            $jsonContent = $starsData = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR)['features'];

            if (isset($id) && !empty($id)) {
                $jsonContent = array_filter($jsonContent, function($tab) use ($id) {
                    return strtolower($tab['properties']['con']) === strtolower($id);
                });

                $starsData["features"] = array_values($jsonContent);
            }
        }

        return new JsonResponse($starsData, Response::HTTP_OK);
    }
}
