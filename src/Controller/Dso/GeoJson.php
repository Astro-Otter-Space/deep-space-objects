<?php

declare(strict_types=1);

namespace App\Controller\Dso;

use App\Managers\DsoManager;
use AstrobinWs\Exceptions\WsException;
use \JsonException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GeoJson
 * @package App\Controller
 */
class GeoJson extends AbstractController
{
    private DsoManager $dsoManager;

    /**
     * GeoJson constructor.
     *
     * @param DsoManager $dsoManager
     */
    public function __construct(DsoManager $dsoManager)
    {
        $this->dsoManager = $dsoManager;
    }

    /**
     * @Route("/geodata/dso/{id}", name="dso_geo_data", options={"expose": true})
     * @param Request $request
     * @param string $id
     *
     * @return JsonResponse
     * @throws JsonException
     * @throws ReflectionException
     * @throws WsException
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        // @todo add check if request is XmlHttpRequest

        $dso = $this->dsoManager->getDso($id);
        $geoJsonData = $dso->geoJson();

        $jsonResponse = new JsonResponse($geoJsonData, Response::HTTP_OK);
        $jsonResponse->setPublic();
        $jsonResponse->setSharedMaxAge(0);

        return $jsonResponse;
    }
}
