<?php

declare(strict_types=1);

namespace App\Controller\Layout;

use App\Controller\ControllerTraits\LayoutTrait;
use App\Controller\LayoutController;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class Header extends AbstractController
{

    use SymfonyServicesTrait, LayoutTrait;

    public function __invoke(Request $request, string $listLocales): Response
    {
        /** @var Request $mainRequest */
        $mainRequest = $this->get('request_stack')->getMainRequest();

        $currentLocale = $mainRequest->getLocale() ?? 'en';

        $listLocales = array_filter(explode('|', $listLocales), static function($value) use ($currentLocale) {
            return !empty($value) && ($value !== $currentLocale);
        });

        $mainRoute = $mainRequest->get('_route') ?? 'homepage';

        $routeParams = $mainRequest->get('_route_params') ?? [];
        $paramsRoute = array_merge($routeParams, $mainRequest->query->all()) ?? [];

        $result = [
            '_route' => $mainRoute,
            'listLocales' => array_map(function($locale) use ($mainRoute, $paramsRoute) {
                $paramsRoute['_locale'] = $locale;
                return [
                    'locale' => $locale,
                    'label' => $this->translator->trans($locale),
                    'flag' => sprintf('flag_%s', $locale),
                    'path' => $this->router->generate(sprintf('%s.%s', $mainRoute, $locale), $paramsRoute)
                ];
            }, $listLocales),
            'currentLocale' => $currentLocale,
            'leftSideMenu' => $this->buildMenu($currentLocale, ['lastUpdate' , 'catalog', 'constellation', 'map', 'contact']),
            'notification' => [
                'label' => $this->translator->trans('last_update_title'),
                'path' => $this->router->generate(sprintf('last_update_dso.%s', $currentLocale))
            ],
            'constellation' => [
                'label' => $this->translator->trans('constId', ['%count%' => 2]),
                'path' => $this->router->generate(sprintf('constellation_list.%s', $currentLocale))
            ],
            'routeSearch' => $this->router->generate(sprintf('search_ajax.%s', $currentLocale), ['_locale' => $currentLocale])
        ];

        $response = new Response();
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);

        return $this->render('includes/layout/header.html.twig', $result, $response);
    }

}
