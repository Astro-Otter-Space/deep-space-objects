<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GenericPageController extends AbstractController
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
    public function skymap(): Response
    {
        $params = [];

        /** @var Response $response */
        $response = $this->render('pages/skymap.html.twig', $params);

        return $response;
    }

}
