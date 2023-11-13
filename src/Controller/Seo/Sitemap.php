<?php

namespace App\Controller\Seo;

use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

final class Sitemap extends AbstractController
{

    use SymfonyServicesTrait;

    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     *
     * @param Request $request
     * @param string $listLocales
     * @param DsoManager $dsoManager
     * @param ConstellationManager $constellationManager
     *
     * @return Response
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(
        Request $request,
        string $listLocales,
        DsoManager $dsoManager,
        ConstellationManager $constellationManager
    ): Response
    {
        $params = [];

        $currentLocal = $request->getLocale();
        $listLocales = array_filter(explode('|', $listLocales), static function($value) use ($currentLocal) {
            return !empty($value) && ($value !== $currentLocal);
        });

        // Static pages
        $params['urls'] = [
            'home' => [
                'loc' => $this->router->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('homepage.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'catalog' => [
                'loc' => $this->router->generate('dso_catalog', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('dso_catalog.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'constellation_list' => [
                'loc' => $this->router->generate('constellation_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('constellation_list.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'contact' => [
                'loc' => $this->router->generate('contact', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('contact.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'skymap' => [
                'loc' => $this->router->generate('skymap', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('skymap.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales)),
            ],
            'last_udapte' => [
                'loc' => $this->router->generate('last_update_dso', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('last_update_dso.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'help_api' => [
                'loc' => $this->router->generate('help_api_page', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('help_api_page.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'legalnotice' => [
                'loc'=> $this->router->generate('legal_notice', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) {
                    return [$locale => $this->router->generate(sprintf('legal_notice.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ],
            'helpus' => [
                'loc'=> $this->router->generate('help_astro-otter', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale)  {
                    return [$locale => $this->router->generate(sprintf('help_astro-otter.%s', $locale), ['_locale' => $locale], UrlGeneratorInterface::ABSOLUTE_URL)];
                }, $listLocales))
            ]
        ];


        /** @var array $listDsoMessier */
        [$listDsoMessier,,] = $dsoManager->getDsoRepository()->getObjectsCatalogByFilters(0, ['catalog' => 'messier'], 110, true);

        /** @var array $listDsoNgc */
        [$listDsoNgc,,] = $dsoManager->getDsoRepository()->getObjectsCatalogByFilters(0, ['catalog' => 'ngc'], 8000, true);

        /** @var DTOInterface $dso */
        foreach ($listDsoMessier as $dsoId) {
            $dso = $dsoManager->getDso($dsoId);
            $params['urls'][$dso->getId()] = [
                'loc' => $dso->absoluteUrl(),
                'urlLoc' => array_merge(...array_map(function ($locale) use ($dsoManager, $dsoId) {
                    $dsoLocal = $dsoManager->getDsoFromCache(sprintf('%s_%s', $dsoId, $locale)) ?? $dsoManager->getDsoRepository()->setLocale($locale)->getObjectById($dsoId);
                    return [$locale => $dsoLocal->absoluteUrl()];
                }, $listLocales)),
                'lastmod' => $dso->getUpdatedAt()->format('Y-m-d')
            ];
        }

        /**foreach ($listDsoNgc as $dso) {
        $params['urls'][$dso->getId()] = [
        'loc' => $dso->fullUrl(),
        'urlLoc' => array_merge(...array_map(static function ($locale) use ($dso) {
        return [$locale => $dso->fullUrl()];
        }, $listLocales)),
        'lastmod' => $dso->getUpdatedAt()->format('Y-m-d')
        ];
        }*/


        $listConstellation = $constellationManager->getAllConstellations(null);

        /** @var ConstellationDTO $constellation */
        foreach ($listConstellation as $constellation) {
            $params['urls'][$constellation->getId()] = [
                'loc' => $constellation->absoluteUrl()
                /*'urlLoc' => array_merge(...array_map(function ($locale) use ($constellation) {
                    return [$locale => $this->urlGenerateHelper->generateUrl($constellation, Router::ABSOLUTE_URL, $locale)];
                }, $listLocales))*/
            ];
        }

        foreach (['messier', 'ngc', 'ic', 'sh'] as $catalog) {
            $params['urls']['catalog_'.$catalog] = [
                'loc' => $this->router->generate('dso_catalog_redirect', ['catalog' => $catalog], UrlGeneratorInterface::ABSOLUTE_URL),
                'urlLoc' => array_merge(...array_map(function ($locale) use ($catalog) {
                    return [$locale => $this->router->generate(sprintf('dso_catalog_redirect.%s', $locale), ['catalog' => $catalog, '_locale' => $locale], Router::ABSOLUTE_URL)];
                }, $listLocales))
            ];
        }

        $xml = $this->renderView('sitemap.xml.twig', $params);

        $response = new Response($xml, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/xml');
        $response->setPublic();
        $response->setSharedMaxAge(0);

        return $response;
    }

}
