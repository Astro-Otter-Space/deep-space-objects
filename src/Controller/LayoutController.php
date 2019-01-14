<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return Response
     */
    public function header(Request $request)
    {
        $result = [];

        dump($request->get('_route'), $request->get('_route_params'));

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
