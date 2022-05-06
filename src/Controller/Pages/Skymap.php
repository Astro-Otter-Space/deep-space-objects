<?php

namespace App\Controller\Pages;

use App\Controller\LayoutController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Skymap extends AbstractController
{

    /**
     * @Route({
     *   "en": "/skymap",
     *   "fr": "/carte-du-ciel",
     *   "es": "/skymap",
     *   "de": "/skymap",
     *   "pt": "/skymap"
     * }, name="skymap")
     */
    public function __invoke(): Response
    {
        $response = $this->render('pages/skymap.html.twig', []);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL)->setPublic();

        return $response;
    }

}
