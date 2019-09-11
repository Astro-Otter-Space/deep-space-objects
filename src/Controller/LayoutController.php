<?php

namespace App\Controller;

use App\Entity\Constellation;
use App\Entity\ListConstellation;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LayoutController
 * @package App\Controller
 */
class LayoutController extends AbstractController
{
    const HTTP_TTL = 31556952;

    /** @var TranslatorInterface  */
    private $translatorInterface;

    /** @var ConstellationRepository */
    private $constellationRepository;

    /** @var DsoRepository */
    private $dsoRepository;

    /** @var UrlGenerateHelper */
    private $urlGenerateHelper;

    /**
     * LayoutController constructor.
     *
     * @param TranslatorInterface $translatorInterface
     * @param ConstellationRepository $constellationRepository
     * @param DsoRepository $dsoRepository
     * @param UrlGenerateHelper $urlGeneratorHelper
     */
    public function __construct(TranslatorInterface $translatorInterface, ConstellationRepository $constellationRepository, DsoRepository $dsoRepository, UrlGenerateHelper $urlGeneratorHelper)
    {
        $this->translatorInterface = $translatorInterface;
        $this->constellationRepository = $constellationRepository;
        $this->dsoRepository = $dsoRepository;
        $this->urlGenerateHelper = $urlGeneratorHelper;
    }

    /**
     * Header
     *
     * @param Request $request
     * @param String $listLocales
     *
     * @return Response
     */
    public function header(Request $request, String $listLocales): Response
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
            'listLocales' => array_map(function($locale) use ($router, $mainRoute, $paramsRoute) {
                $paramsRoute['_locale'] = $locale;
                return [
                    'locale' => $locale,
                    'label' => $this->translatorInterface->trans($locale),
                    'flag' => sprintf('flag_%s', $locale),
                    'path' => $router->generate(sprintf('%s.%s', $mainRoute, $locale), $paramsRoute)
                ];
            }, $listLocales),
            'currentLocale' => $currentLocale,
            'leftSideMenu' => $this->leftSideMenu($currentLocale),
        ];

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);
        return $this->render('includes/layout/header.html.twig', $result, $response);
    }


    /**
     * Build left side menu
     *
     * @param string $locale
     *
     * @return array
     */
    private function leftSideMenu($locale = 'en')
    {
        /** @var Router $routerInterface */
        $routerInterface = $this->get('router');

        return [
            'catalog' => [
                'label' => $this->translatorInterface->trans('catalogs'),
                'path' => $routerInterface->generate(sprintf('dso_catalog.%s', $locale)),
                'icon_class' => 'galaxy-cluster'
            ],
            'constellation' => [
                'label' => $this->translatorInterface->trans('constId', ['%count%' => 2]),
                'path' => $routerInterface->generate(sprintf('constellation_list.%s', $locale)),
                'icon_class' => 'constellation'
            ],
            'observations' => [
                'label' => $this->translatorInterface->trans('listObservations'),
                'path' => $routerInterface->generate(sprintf('observation_list.%s', $locale)),
                'icon_class' => 'telescop'
            ],
            'map' => [
                'label' => $this->translatorInterface->trans('skymap'),
                'path' => $routerInterface->generate(sprintf('skymap.%s', $locale)),
                'icon_class' => 'planet'
            ],
            'contact' => [
                'label' => $this->translatorInterface->trans('contact.title'),
                'path' => $routerInterface->generate(sprintf('contact.%s', $locale)),
                'icon_class' => 'contact'
            ]
        ];
    }

    /**
     * Footer
     * @deprecated
     * @param Request $request
     * @return Response
     */
    public function footer(Request $request, $githubLink, $paypalLink,$facebookLink)
    {
        /** @var Router $routerInterface */
        $routerInterface = $this->get('router');

        $result['share'] = $this->ctaFooter($githubLink, $paypalLink, $facebookLink);

        $result['links_footer'] = [
            'api' => [
                'label' => 'API',
                'path' => $routerInterface->generate(sprintf('help_api_page.%s', $request->getLocale()))
            ],
            'legal_notice' => [
                'label' => $this->translatorInterface->trans('legal_notice.title'),
                'path' => $routerInterface->generate(sprintf('legal_notice.%s', $request->getLocale())),
            ],
            'contact' => [
                'label' => $this->translatorInterface->trans('contact.title'),
                'path' => $routerInterface->generate(sprintf('contact.%s', $request->getLocale())),
            ]
        ];

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);

        return $this->render('includes/layout/footer.html.twig', $result, $response);
    }


    /**
     * @param $githubLink
     * @param $paypalLink
     * @param $facebookLink
     *
     * @return array
     */
    private function ctaFooter($githubLink, $paypalLink, $facebookLink)
    {
        return [
            'github' => [
                'label' => ucfirst('github'),
                'path' => $githubLink,
                'blank' => true,
                'icon_class' => 'github'
            ],
            'paypal' => [
                'label' => ucfirst('paypal'),
                'path' => $paypalLink,
                'blank' => true,
                'icon_class' => 'paypal'
            ],
            'facebook' => [
                'label' => ucfirst('facebook'),
                'path' => $facebookLink,
                'blank' => true,
                'icon_class' => 'facebook'
            ]
        ];
    }

    /**
     * @Route("/sitemap.xml", name="sitemap")
     *
     * @param Request $request
     * @param string $listLocales
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function sitemap(Request $request, string $listLocales)
    {
        $params = [];

        $currentLocal = $request->getLocale();

        $listLocales = array_filter(explode('|', $listLocales), function($value) use ($currentLocal) {
            return !empty($value) && ($value !== $currentLocal);
        });

        /** @var Router $router */
        $router = $this->get('router');

        // Static pages
        $params['urls'] = [
            'home' => [
                'loc' => $router->generate('homepage', [],Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('homepage.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'catalog' => [
                'loc' => $router->generate('dso_catalog', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('dso_catalog.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'constellation_list' => [
                'loc' => $router->generate('constellation_list', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('constellation_list.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'contact' => [
                'loc' => $router->generate('contact', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('contact.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'skymap' => [
                'loc' => $router->generate('skymap', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('skymap.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales)),
            ],
            'obs_list' => [
                'loc' => $router->generate('observation_list', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('observation_list.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'add_obs' => [
                'loc' => $router->generate('add_observation', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('add_observation.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'help_api' => [
                'loc' => $router->generate('help_api_page', [], Router::ABSOLUTE_URL)
            ]
        ];

        /** @var  $listDso */
        list($listDso,,) = $this->dsoRepository->getObjectsCatalogByFilters(0, ['catalog' => 'messier'], 1000);
        foreach ($listDso as $dso) {
            $params['urls'][$dso->getId()] = [
                'loc' => $this->urlGenerateHelper->generateUrl($dso, Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($dso) {
                    return [
                        $locale => $this->urlGenerateHelper->generateUrl($dso, Router::ABSOLUTE_URL, $locale)
                    ];
                }, $listLocales))
            ];
        }

        /** @var ListConstellation $listConstellation */
        $listConstellation = $this->constellationRepository->getAllConstellation();

        /** @var Constellation $constellation */
        foreach ($listConstellation as $constellation) {
            $params['urls'][$constellation->getId()] = [
                'loc' => $this->urlGenerateHelper->generateUrl($constellation, Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($constellation) {
                    return [
                        $locale => $this->urlGenerateHelper->generateUrl($constellation, Router::ABSOLUTE_URL, $locale)
                    ];
                }, $listLocales))
            ];
        }

        /** @var Response $response */
        $response = new Response();
        $response->headers->set('Content-Type', ['text/xml', 'application/xml']);

        return $this->render('sitemap.xml.twig', $params, $response);
    }
}
