<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\DsoDataTransformer;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Services\GetImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DebugController extends AbstractController
{


    /**
     * @Route("/debug/astrobin/image/{id}", name="debug_astrobin_image")
     * @param Request $request
     * @param string $id
     *
     * @return Response
     * @throws WsException
     * @throws WsResponseException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function debugAstrobinImage(Request $request, string $id): Response
    {
        $imageWs = new GetImage();
        try {
            $image = $imageWs->getById($id);
        } catch (WsException $e) {
            var_dump($e->getMessage());
        }


        return $this->render('pages/debug.html.twig', ['data' => $image]);
    }

    /**
     * @param Request $request
     *
     * @param int $offset
     * @param DsoRepository $dsoRepository
     * @param DsoManager $dsoManager
     * @param DsoDataTransformer $dataTransformer
     *
     * @return Response
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     * @Route("/debug/astrobin/list/{offset}", name="debug_astrobin_list")
     */
    public function debugListAstrobinImage(Request $request, int $offset, DsoRepository $dsoRepository, DsoManager $dsoManager, DsoDataTransformer $dataTransformer): Response
    {
        $items = $dsoRepository->getAstrobinId(null);
        ksort($items);
        $items = array_slice($items, $offset, 50);
        $listDso = $dsoManager->buildListDso(array_keys($items));
        $params['dso'] = $dataTransformer->listVignettesView($listDso);

        return $this->render('pages/debug_astrobin.html.twig', $params);
    }
}
