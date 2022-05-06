<?php

namespace App\Controller;

use App\DataTransformer\DsoDataTransformer;
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
    private DsoManager $dsoManager;

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
     * @var Request $request
     * @param DsoDataTransformer $dataTransformer
     *
     * @return Response
     * @throws \AstrobinWs\Exceptions\WsException
     * @throws \JsonException
     * @throws \ReflectionException

     */
    public function vignettesDso(Request $request, DsoDataTransformer $dataTransformer): Response
    {
        $vignettes = $this->dsoManager->randomDsoWithImages(self::DSO_VIGNETTES);
        $params['vignettes'] = $dataTransformer->listVignettesView($vignettes);

        /** @var Response $response */
        $response = new Response();
        $response->setPublic();

        // TTL of 24 hours
        $response->setSharedMaxAge(86400);

        return $this->render('includes/components/vignettes.html.twig', $params, $response);
    }

}
