<?php

declare(strict_types=1);

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpAstroOtter extends AbstractController
{

    /**
     * @Route({
     *   "en": "/support-astro-otter",
     *   "fr": "/soutenir-le-site"
     * }, name="help_astro-otter")
     *
     * @param Request $request
     * @param string|null $paypalLink
     * @param string|null $tipeeeLink
     *
     * @return Response
     */
    public function __invoke(Request $request, ?string $paypalLink, ?string $tipeeeLink): Response
    {
        $params['links'] = [
            'paypal' => [
                'label' => ucfirst('paypal'),
                'path' => $paypalLink,
                'blank' => true,
                'icon_class' => 'paypal'
            ],
            'tipeee' => [
                'label' => ucfirst('tipeee'),
                'path' => $tipeeeLink,
                'blank' => true,
                'icon_class' => 'tipeee'
            ]
        ];

        return $this->render('pages/support.html.twig', $params);
    }
}
