<?php

declare(strict_types=1);

namespace App\Controller\Layout;

use App\DataTransformer\ConstellationDataTransformer;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\ES\ListConstellation;
use App\Entity\ES\ListDso;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class SearchAutocomplete extends AbstractController
{

    /**
     * @Route(
     *     "/_search",
     *     options={"expose"=true},
     *     name="search_ajax"
     * )
     * @param Request $request
     * @param DsoDataTransformer $dsoDataTransformer
     * @param DsoManager $dsoManager
     * @param ConstellationDataTransformer $constellationDataTransformer
     * @param ConstellationManager $constellationManager
     *
     * @return JsonResponse
     */
    public function __invoke(
        Request $request,
        DsoDataTransformer $dsoDataTransformer,
        DsoManager $dsoManager,
        ConstellationDataTransformer $constellationDataTransformer,
        ConstellationManager $constellationManager
    ): JsonResponse
    {
        $data = [];
        if ($request->query->has('q')) {
            $listDso = null;
            $searchTerm = strtolower(filter_var($request->query->get('q'), FILTER_SANITIZE_STRING));
            try {
                $listDso = $dsoManager->searchDsoByTerms($searchTerm, null);
            } catch (WsException|\JsonException|\ReflectionException $e) {
            } finally {
                $dataDso = (!is_null($listDso)) ? $dsoDataTransformer->listVignettesView($listDso) : [];
            }

            try {
                $listConstellation = $constellationManager->searchConstellationsByTerms($searchTerm);
            } catch (\JsonException $e) {
            } finally {
                $dataConstellation = $constellationDataTransformer->listVignettesView($listConstellation);
                $listConstellation = new ListConstellation();
            }
            $data = array_merge($dataDso, $dataConstellation);
        }

        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }

}
