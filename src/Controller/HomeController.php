<?php

namespace App\Controller;

use App\Managers\DsoManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public const DSO_VIGNETTES = 3;

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
     * @param Request $request
     *
     * @return Response
     */
    public function homepage(Request $request): Response
    {

        /** @var Response $response */
        $response = $this->render('pages/home.html.twig', ['currentLocale' => $request->getLocale()]);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);
        $response->setPublic();

        return $response;
    }

    /**
     * @return Response
     * @throws \Exception
     * @var Request $request
     */
    public function vignettesDso(Request $request): Response
    {
        $params['vignettes'] = $this->dsoManager->randomDsoWithImages(self::DSO_VIGNETTES);

        /** @var Response $response */
        $response = new Response();
        $response->setPublic();

        // TTL of 24 hours
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
