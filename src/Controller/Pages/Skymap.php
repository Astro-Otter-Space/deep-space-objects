<?php

declare(strict_types=1);

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class Skymap extends AbstractController
{

    public const HTTP_TTL = 31556952;

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
        $response->setSharedMaxAge(self::HTTP_TTL)->setPublic();

        return $response;
    }

}
