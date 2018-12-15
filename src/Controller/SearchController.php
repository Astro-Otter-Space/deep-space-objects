<?php

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
     * @Route(
     *     "/_research",
     *     options={"expose"=true},
     *     name="search_ajax"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAjax(Request $request)
    {
        $data = [];
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        return $response;
    }

}
