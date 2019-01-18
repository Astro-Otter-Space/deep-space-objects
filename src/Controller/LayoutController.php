<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LayoutController
 * @package App\Controller
 */
class LayoutController extends AbstractController
{
    const HTTP_TTL = 31556952;


    /**
     * Header
     *
     * @param Request $request
     * @param String $listLocales
     * @param TranslatorInterface $translatorInterface
     * @return Response
     */
    public function header(Request $request, String $listLocales, TranslatorInterface $translatorInterface): Response
    {
        /** @var Request $mainRequest */
        $mainRequest = $this->get('request_stack')->getMasterRequest();

        /** @var Router $router */
        $router = $this->get('router');

        $currentLocale = $mainRequest->getLocale();

        $listLocales = array_filter(explode('|', $listLocales), function($value) use ($currentLocale) {
            return !empty($value) && ($value !== $currentLocale);
        });

        $mainRoute = $mainRequest->get('_route');
        $paramsRoute = array_merge($mainRequest->get('_route_params'), $mainRequest->query->all());
        $result = [
            '_route' => $mainRoute,
            'listLocales' => array_map(function($locale) use ($router, $translatorInterface, $mainRoute, $paramsRoute) {
                $paramsRoute['_locale'] = $locale;
                return [
                    'locale' => $locale,
                    'label' => $translatorInterface->trans($locale),
                    'path' => $router->generate(sprintf('%s.%s', $mainRoute, $locale), $paramsRoute)
                ];
            }, $listLocales),
            'currentLocale' => $currentLocale
        ];

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);
        return $this->render('includes/layout/header.html.twig', $result, $response);
    }

    /**
     * Footer
     *
     * @param Request $request
     * @return Response
     */
    public function footer(Request $request)
    {
        $result = [];

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);

        return $this->render('includes/layout/footer.html.twig', $result, $response);
    }

}
