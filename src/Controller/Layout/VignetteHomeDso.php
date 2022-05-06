<?php

namespace App\Controller\Layout;

use App\DataTransformer\DsoDataTransformer;
use App\Managers\DsoManager;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VignetteHomeDso extends AbstractController
{

    public const DSO_VIGNETTES = 3;

    /**
     * @param Request $request
     * @param DsoManager $dsoManager
     * @param DsoDataTransformer $dataTransformer
     *
     * @return Response
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(
        Request $request,
        DsoManager $dsoManager,
        DsoDataTransformer $dataTransformer
    ): Response
    {
        $vignettes = $dsoManager->randomDsoWithImages(self::DSO_VIGNETTES);
        $params['vignettes'] = $dataTransformer->listVignettesView($vignettes);

        $response = new Response();
        $response->setPublic();

        // TTL of 24 hours
        $response->setSharedMaxAge(86400);

        return $this->render('includes/components/vignettes.html.twig', $params, $response);
    }

}
