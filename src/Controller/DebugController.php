<?php

declare(strict_types=1);

namespace App\Controller;

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
     * @Route("/debug/astrobin/{id}", name="debug_astrobin")
     * @param Request $request
     * @param string $id
     *
     * @return Response
     * @throws WsException
     * @throws WsResponseException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function testAstrobin(Request $request, string $id): Response
    {
        $imageWs = new GetImage();
        $image = $imageWs->getById($id);

        return new Response($image);
    }
}
