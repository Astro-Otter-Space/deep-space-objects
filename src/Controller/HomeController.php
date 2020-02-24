<?php

namespace App\Controller;

use App\Managers\DsoManager;
use Elastica\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /** @var DsoManager  */
    private $dsoManager;

    /**
     * HomeController constructor.
     *
     * @param DsoManager $dsoManager
     */
    public function __construct(DsoManager $dsoManager)
    {
        $this->dsoManager = $dsoManager;
    }

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
     * @var Request $request
     * @return Response
     */
    public function vignetteDso(Request $request): Response
    {
        $params = [];

        $params['vignettes'] = [];

        /** @var Response $response */
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge(86400);

        return $this->render('includes/components/vignettes.html.twig', $params, $response);
    }

    /**
     * @param string $env
     * @Route("/phpinfo", name="phpinfo")
     */
    public function phpinfo($env)
    {
        echo ('prod' !== $env) ? phpinfo() : new Response();
    }
}
