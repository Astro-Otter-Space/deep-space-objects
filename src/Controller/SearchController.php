<?php

namespace App\Controller;

use App\Entity\Dso;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController
 * @package App\Controller
 */
class SearchController extends AbstractController
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
}
