<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{

    /**
     * Homepage
     *
     * @Route("/", name="homepage")
     * @return Response
     */
    public function homepage(): Response
    {
        /** @var Response $response */
        $response = $this->render('pages/home.html.twig', []);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);
        $response->setPublic();

        return $response;
    }

    /**
     * @param string $env
     * @Route("/phpinfo", name="phpinfo")
     */
    public function phpinfo($env)
    {
        echo ('prod' === $env) ? phpinfo() : new Response();
    }
}
