<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SearchController
 * @package App\Controller
 */
class SearchController extends AbstractController
{

    /**
     * @deprecated ?
     * @Route(
     *     "/build/data/stars.{id}.json",
     *     options={"expose"=true},
     *     name="list_stars"
     * )
     * @param Request $request
     * @param string $id
     *
     * @return JsonResponse
     * @throws \JsonException
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
            if (!empty($id)) {
                $jsonContent = array_filter($jsonContent, static function($tab) use ($id) {
                    return strtolower($tab['properties']['con']) === strtolower($id);
                });
                $starsData["features"] = array_values($jsonContent);
            }
        }

        return new JsonResponse($starsData, Response::HTTP_OK);
    }
}
