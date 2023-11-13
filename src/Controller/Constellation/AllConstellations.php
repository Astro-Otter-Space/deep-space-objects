<?php

declare(strict_types=1);

namespace App\Controller\Constellation;

use App\DataTransformer\ConstellationDataTransformer;
use App\Managers\ConstellationManager;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AllConstellations extends AbstractController
{
    public const HTTP_TTL = 31556952;

    /**
     * @Route("/constellations", name="constellation_list")
     *
     * @param Request $request
     * @param ConstellationManager $constellationManager
     * @param ConstellationDataTransformer $constellationDataTransformer
     *
     * @return Response
     * @throws ReflectionException
     * @throws \JsonException
     */
    public function __invoke(
        Request $request,
        ConstellationManager $constellationManager,
        ConstellationDataTransformer $constellationDataTransformer
    ): Response
    {
        $result = [];
        $listConstellations = $constellationManager->getAllConstellations(null);
        $result['list_constellation'] = $constellationDataTransformer->listVignettesView($listConstellations);

        $response = $this->render('pages/constellations.html.twig', $result);
        $response->setSharedMaxAge(self::HTTP_TTL)->setPublic();

        return $response;
    }

}
