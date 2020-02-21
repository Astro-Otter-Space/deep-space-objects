<?php

namespace App\Controller;

use App\Entity\ES\Constellation;
use App\Entity\ES\Dso;
use App\Entity\ES\ListConstellation;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
//            'leftSideMenu' => $this->buildMenu($currentLocale),
            'menuData' => $this->buildMenu($currentLocale, ['catalog', 'constellation', 'map']),
            'menuObservations' => $this->buildMenu($currentLocale, ['observations', 'addObservations', 'scheduleObs'])
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
     * @param array $listKeysMenu
     * @return array
     */
    private function buildMenu(string $locale = 'en', array $listKeysMenu): array
    {
        /** @var Router $routerInterface */
        $routerInterface = $this->get('router');

        $menu = [
            'catalog' => [
                'label' => $this->translatorInterface->trans('catalogs'),
                'path' => $routerInterface->generate(sprintf('dso_catalog.%s', $locale)),
                'icon_class' => 'shape'
            ],
            'constellation' => [
                'label' => $this->translatorInterface->trans('constId', ['%count%' => 2]),
                'path' => $routerInterface->generate(sprintf('constellation_list.%s', $locale)),
                'icon_class' => 'constellation'
            ],
            'map' => [
                'label' => $this->translatorInterface->trans('skymap'),
                'path' => $routerInterface->generate(sprintf('skymap.%s', $locale)),
                'icon_class' => 'planet'
            ],
            'observations' => [
                'label' => $this->translatorInterface->trans('listObservations'),
                'path' => $routerInterface->generate(sprintf('observation_list.%s', $locale)),
                'icon_class' => 'telescop'
            ],
            'addObservations' => [
                'label' => $this->translatorInterface->trans('addObservation'),
                'path' => $routerInterface->generate(sprintf('add_observation.%s', $locale)),
                'icon_class' => 'add-observation'
            ],
            'scheduleObs' => [
                'label' => $this->translatorInterface->trans('scheduleObs'),
                'path' => $routerInterface->generate(sprintf('schedule_obs.%s', $locale)),
                'icon_class' => 'calendar'
            ],
            'contact' => [
                'label' => $this->translatorInterface->trans('contact.title'),
                'path' => $routerInterface->generate(sprintf('contact.%s', $locale)),
                'icon_class' => 'contact'
            ]
        ];

        return array_filter($menu, function ($key) use ($listKeysMenu) {
            return in_array($key, $listKeysMenu);
        }, ARRAY_FILTER_USE_KEY);

    }

    /**
     * Footer
     *
     * @param Request $request
     * @param $githubLink
     * @param $paypalLink
     * @param $facebookLink
     *
     * @return Response
     * @deprecated
     */
    public function footer(Request $request, ?string $githubLink, ?string $paypalLink, ?string $facebookLink, ?string $twitterLink): Response
    {
        /** @var Request $mainRequest */
        $mainRequest = $this->get('request_stack')->getMasterRequest();
        $mainRoute = $mainRequest->get('_route');

        /** @var Router $routerInterface */
        $routerInterface = $this->get('router');

        $result['share'] = $this->ctaFooter($githubLink, $paypalLink, $facebookLink, $twitterLink);

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

        $result['main_route'] = $mainRoute;

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(0);

        return $this->render('includes/layout/footer.html.twig', $result, $response);
    }


    /**
     * @param string $githubLink
     * @param string $paypalLink
     * @param string $facebookLink
     * @param string $twitterLink
     *
     * @return array
     */
    private function ctaFooter(?string $githubLink, ?string $paypalLink, ?string $facebookLink, ?string $twitterLink): array
    {
        $tab = [];

        if ($githubLink) {
            $tab['github'] = [
                'label' => ucfirst('github'),
                'path' => $githubLink,
                'blank' => true,
                'icon_class' => 'github'
            ];
        }

        if ($paypalLink) {
            $tab['paypal'] = [
                'label' => ucfirst('paypal'),
                'path' => $paypalLink,
                'blank' => true,
                'icon_class' => 'paypal'
            ];
        }

        if ($facebookLink) {
            $tab['facebook'] = [
                'label' => ucfirst('facebook'),
                'path' => $facebookLink,
                'blank' => true,
                'icon_class' => 'facebook'
            ];
        }

        if ($twitterLink) {
            $tab['twitter'] = [
                'label' => ucfirst('twitter'),
                'path' => $twitterLink,
                'blank' => true,
                'icon_class' => 'twitter'
            ];
        }

        return $tab;
    }

    /**
     * @Route("/sitemap.xml", name="sitemap", format="xml")
     *
     * @param Request $request
     * @param string $listLocales
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function sitemap(Request $request, string $listLocales): Response
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
            /*'add_event' => [
                'loc' => $router->generate('schedule_obs', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('schedule_obs.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],*/
            'add_obs' => [
                'loc' => $router->generate('add_observation', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('add_observation.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'help_api' => [
                'loc' => $router->generate('help_api_page', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('help_api_page.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'legalnotice' => [
                'loc'=> $router->generate('legal_notice', [], Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($router) {
                    return [$locale => $router->generate(sprintf('legal_notice.%s', $locale), ['_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ]
        ];

        /** @var  $listDso */
        [$listDsoMessier,,] = $this->dsoRepository->getObjectsCatalogByFilters(0, ['catalog' => 'messier'], 1000);
        [$listDsoNgc,,] = $this->dsoRepository->getObjectsCatalogByFilters(0, ['catalog' => 'ngc'], 8000);

        /** @var Dso $dso */
        foreach ($listDsoMessier as $dso) {
            $params['urls'][$dso->getId()] = [
                'loc' => $this->urlGenerateHelper->generateUrl($dso, Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($dso) {
                    return [
                        $locale => $this->urlGenerateHelper->generateUrl($dso, Router::ABSOLUTE_URL, $locale)
                    ];
                }, $listLocales)),
                'lastmod' => $dso->getUpdatedAt()->format('Y-m-d')
            ];
        }
        foreach ($listDsoNgc as $dso) {
            $params['urls'][$dso->getId()] = [
                'loc' => $this->urlGenerateHelper->generateUrl($dso, Router::ABSOLUTE_URL),
                'urlLoc' => call_user_func_array("array_merge", array_map(function($locale) use ($dso) {
                    return [
                        $locale => $this->urlGenerateHelper->generateUrl($dso, Router::ABSOLUTE_URL, $locale)
                    ];
                }, $listLocales)),
                'lastmod' => $dso->getUpdatedAt()->format('Y-m-d')
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

        $xml = $this->renderView('sitemap.xml.twig', $params);

        /** @var Response $response */
        $response = new Response($xml, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        $response->setSharedMaxAge(0);

        return $response;
    }


    /**
     * @Route("/load/data/{file}", name="data_celestial")
     * @param Request $request
     * @param KernelInterface $kernel
     * @param string $file
     *
     * @return JsonResponse
     */
    public function getStarsFromConst(Request $request, KernelInterface $kernel, string $file): JsonResponse
    {
        preg_match('/stars.([A-Za-z]{3}|([0-9]{1,2})).json/', $file, $matches);
        $json = [];
        if ($matches) {
            $match = $matches[1];
            if (in_array($match, [6,8,14])) {
                $fileJson = file_get_contents($kernel->getProjectDir() . '/public/build/data/' . sprintf('stars.%d.json', $match));

                $json = json_decode($fileJson);
            } else {
                $fileJson = file_get_contents($kernel->getProjectDir() . '/public/build/data/stars.14.json');

                $dataJson = json_decode($fileJson, true);
                $filteredStars = array_filter($dataJson['features'], function ($starData) use ($match) {
                    return $match === $starData['properties']['con'];
                });

                $json = [
                    'type' => 'FeatureCollection',
                    'features' => array_values($filteredStars)
                ];
            }
        } else {
            $filePath = $kernel->getProjectDir() . sprintf('/public/build/data/%s', $file);
            if (file_exists($filePath)) {
                $fileJson = file_get_contents($filePath);
                $json = json_decode($fileJson);
            }
        }

        return new JsonResponse($json, Response::HTTP_OK);
    }

}
