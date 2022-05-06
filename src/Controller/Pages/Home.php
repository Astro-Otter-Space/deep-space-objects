<?php

declare(strict_types=1);

namespace App\Controller\Pages;

use App\Controller\LayoutController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class Home extends AbstractController
{

    /**
     * Homepage
     *
     * @Route("/", name="homepage")
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $response = $this->render('pages/home.html.twig', ['currentLocale' => $request->getLocale()]);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);
        $response->setPublic();

        return $response;
    }
}
