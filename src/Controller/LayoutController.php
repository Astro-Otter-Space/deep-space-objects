<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LayoutController
 * @package App\Controller
 */
class LayoutController extends AbstractController
{
    const HTTP_TTL = 31556952;


    /**
     * Header
     *
     * @param Request $request
     * @param String $listLocales
     * @return Response
     */
    public function header(Request $request, String $listLocales): Response
    {
        /** @var Request $mainRequest */
        $mainRequest = $this->get('request_stack')->getMasterRequest();

        $result = [
            '_route' => $mainRequest->get('_route'),
            'params' => array_merge($mainRequest->get('_route_params'), $mainRequest->query->all()),
            'listLocales' => array_filter(explode('|', $listLocales), function($value) { return !empty($value); }),
            'currentLocale' => $mainRequest->getLocale()
        ];

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);
        return $this->render('includes/layout/header.html.twig', $result, $response);
    }

    /**
     * Footer
     *
     * @param Request $request
     * @return Response
     */
    public function footer(Request $request)
    {
        $result = [];

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);

        return $this->render('includes/layout/footer.html.twig', $result, $response);
    }

}
