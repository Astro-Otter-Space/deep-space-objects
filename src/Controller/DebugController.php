<?php

namespace App\Controller;

use App\DataTransformer\DsoDataTransformer;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use App\Service\NotificationService;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Services\GetImage;
use AstrobinWs\Services\GetUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DebugController
 * @package App\Controller
 */
class DebugController extends AbstractController
{

    #[Route('/publish', name='debug_mercure_publisher')]
    public function debugPublish(
        Request $request, 
        NotificationService $notificationService
    ): Response
    {
    	try {
    	    $publish = $notificationService->send('coucou');
    	    echo '<pre>Send: '; var_dump($publish);
    	} catch (\Exception $e) {
    	    echo '<pre>Error: '; var_dump($e);
    	}
    	
	return new Response($publish);
    }

    /**
     * @Route("/astrobin/image/{id}", name="debug_astrobin_image")
     * @param Request $request
     * @param string $id
     *
     * @return Response
     * @throws WsResponseException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function debugAstrobinImage(Request $request, string $id): Response
    {
        $imageWs = new GetImage(null, null);
        $userWs = new GetUser(null, null);
        try {
            $image = $imageWs->getById($id);
            $user = $userWs->getByUsername($image->user, 1);
        } catch (WsException $e) {
            var_dump($e->getMessage());
        }

        return $this->render('pages/debug.html.twig', ['image' => $image, 'user' => $user]);
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
     * @Route("/astrobin/list/{offset}", name="debug_astrobin_list")
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
