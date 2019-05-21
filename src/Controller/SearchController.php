<?php

namespace App\Controller;

use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Managers\ObservationManager;
use FOS\RestBundle\Controller\Annotations as Rest;
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

    /**
     * @Route(
     *     "/_search",
     *     options={"expose"=true},
     *     name="search_ajax"
     * )
     *
     * @param Request $request
     * @param DsoManager $dsoManager
     * @param ConstellationManager $constellationManager
     * @return JsonResponse
     */
    public function searchAjax(Request $request, DsoManager $dsoManager, ConstellationManager $constellationManager)
    {
        $data = [];
        if ($request->query->has('q')) {
            $searchTerm = filter_var($request->query->get('q'), FILTER_SANITIZE_STRING);
            $dataDso = $dsoManager->searchDsoByTerms($searchTerm);

            $dataConstellation = $constellationManager->searchConstellationsByTerms($searchTerm);
            $data = array_merge($dataDso, $dataConstellation);
        }

        /** @var JsonResponse $response */
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        return $response;
    }


    /**
     * @Route(
     *     "/_search-observation",
     *     name="search_observation_ajax",
     *     options={"exposes"=true}
     * )
     * @param Request $request
     * @param ObservationManager $observationManager
     *
     * @return JsonResponse
     */
    public function searchObservationAjax(Request $request, ObservationManager $observationManager)
    {
        $data = [];
        if ($request->query->has('q')) {
            $searchTerm = filter_var($request->query->get('q'), FILTER_SANITIZE_STRING);
            $data = $observationManager->buildSearchObservationByTerms($searchTerm);
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
    public function starsFiltered(Request $request, $id)
    {
        $webPath = $this->getParameter('kernel.project_dir') . '/public/';
        $file = $webPath . 'build/data/stars.8.json';

        if (file_exists($file)) {

            $jsonContent = $starsData = json_decode(file_get_contents($file), true)['features'];

            if (isset($id) && !empty($id)) {
                $jsonContent = array_filter($jsonContent, function($tab) use ($id) {
                    return strtolower($tab['properties']['con']) === strtolower($id);
                });

                $starsData = [
                    "type"  => "FeatureCollection",
                    "features" => array_values($jsonContent)
                ];
            }
        }

        return new JsonResponse($starsData, Response::HTTP_OK);
    }
}
