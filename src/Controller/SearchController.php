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
     *     "/_search",
     *     options={"expose"=true},
     *     name="search_ajax"
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAjax(Request $request)
    {

        if ($request->request->has('q')) {
            $searchTerm = $request->request->get('q');

        }

        $data = [
            [
                'id' => 1,
                'value' => 'M42',
                'label' => 'M42 - Orion nebula'
            ],
            [
                'id' => 2,
                'value' => 'M31',
                'label' => 'M31 - Andromeda galaxy'
            ],
            [
                'id' => 3,
                'value' => 'IC1101',
                'label' => 'IC 1101 - Galaxy'
            ],
            [
                'id' => 4,
                'value' => 'NGC2772',
                'label' => 'NGC 2772 - Spiral galaxy'
            ]
        ];

        /** @var JsonResponse $response */
        $response = new JsonResponse($data, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        return $response;
    }

}
