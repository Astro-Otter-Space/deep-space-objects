<?php

declare(strict_types=1);

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class Home extends AbstractController
{
    public const HTTP_TTL = 31556952;

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
        $response->setSharedMaxAge(self::HTTP_TTL);
        $response->setPublic();

        return $response;
    }
}
