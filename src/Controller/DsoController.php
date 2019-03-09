<?php

namespace App\Controller;

use App\Classes\CacheInterface;
use App\Entity\Dso;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use App\Service\MemcacheService;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Response\Image;
use Astrobin\Response\ListImages;
use Astrobin\Services\GetImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoController
 * @package App\Controller
 */
class DsoController extends AbstractController
{

    /**
     * @Route({
     *  "en": "/catalog/{id}",
     *  "fr": "/catalogue/{id}",
     *  "es": "/catalogo/{id}",
     *  "pt": "/catalogo/{id}",
     *  "de": "/katalog/{id}"
     * }, name="dso_show")
     *
     * @param string $id
     * @param DsoManager $dsoManager
     * @param CacheInterface $cacheUtil
     *
     * @return Response
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    public function show(string $id, DsoManager $dsoManager, CacheInterface $cacheUtil)
    {
        $params = [];

        /** @var Dso $dso */
        $dso = $dsoManager->buildDso($id);

        if (!is_null($dso)) {
            $params['dso'] = $dsoManager->formatVueData($dso);
            $params['constTitle'] = $dsoManager->buildTitleConstellation($dso->getConstId());
            $params['title'] = $dsoManager->buildTitle($dso);
            $params['imgCover'] = $dso->getImage();
            $params['imgCoverUser'] = $dso->getAstrobinUser();
            $params['geojsonDso'] = $dsoManager->buildgeoJson($dso);
            $params['images'] = [];
            // List of Dso from same constellation
            $params['dso_by_const'] = $dsoManager->getListDsoFromConst($dso, 20);

            try {
                if ($cacheUtil->hasItem(md5($id . '_list_images'))) {
                    $params['images'] = unserialize($cacheUtil->getItem(md5($id . '_list_images')));
                } else {
                    $params['images'] = $this->getListImages($dso->getId(), $cacheUtil);
                }
            } catch (WsResponseException $e) {}
        }

        /** @var Response $response */
        $response = $this->render('pages/dso.html.twig', $params);
        $response->setPublic();
        $response->setSharedMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set('x-dso-id', $dso->getElasticId());

        return $response;
    }


    /**
     * Retrieve list of images for carousel

     * @param $dsoId
     * @param CacheInterface $cacheUtil
     *
     * @return array
     * @throws WsResponseException
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    private function getListImages($dsoId, CacheInterface $cacheUtil)
    {
        $tabImages = [];

        /** @var GetImage $astrobinWs */
        $astrobinWs = new GetImage();

        /** @var ListImages|Image $listImages */
        $listImages = $astrobinWs->getImagesBySubject($dsoId, 5);

        if ($listImages instanceof Image) {
            $tabImages = $listImages->url_regular;

        } elseif ($listImages instanceof ListImages && 0 < $listImages->count) {
            $tabImages = array_map(function (Image $image) {
                return $image->url_regular;
            }, iterator_to_array($listImages));
        }

        $cacheUtil->saveItem(md5($dsoId . '_list_images'), serialize($tabImages));
        return $tabImages;
    }

    /**
     * @Route("/geodata/dso/{id}", name="dso_geo_data", options={"expose": true})
     * @param string $id
     * @param DsoManager $dsoManager
     *
     * @return JsonResponse
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    public function geoJson(string $id, DsoManager $dsoManager)
    {
        /** @var Dso $dso */
        $dso = $dsoManager->buildDso($id);

        $geoJsonData = $dsoManager->buildgeoJson($dso);

        /** @var JsonResponse $jsonResponse */
        $jsonResponse = new JsonResponse($geoJsonData, Response::HTTP_OK);
        $jsonResponse->setPublic();
        $jsonResponse->setSharedMaxAge(0);

        return $jsonResponse;
    }


    /**
     * @Route({
     *  "en": "/catalog",
     *  "fr": "/catalogue",
     *  "es": "/catalogo",
     *  "pt": "/catalogo",
     *  "de": "/katalog"
     * }, name="dso_catalog")
     *
     * @param Request $request
     * @param DsoRepository $dsoRepository
     * @param DsoManager $dsoManager
     * @param TranslatorInterface $translatorInterface
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function catalog(Request $request, DsoRepository $dsoRepository, DsoManager $dsoManager, TranslatorInterface $translatorInterface)
    {
        $page = 1;
        $from = DsoRepository::FROM;
        $filters = [];

        /** @var Router $router */
        $router = $this->get('router');

        if ($request->query->has('page')) {
            $page = (int)filter_var($request->query->get('page'), FILTER_SANITIZE_NUMBER_INT);
            if (is_int($page)) {
                $from = (DsoRepository::SIZE)*($page-1);
            }
        }

        if (0 < $request->query->count()) {
            $authorizedFilters = $dsoRepository->getListAggregates(true);

            // Removed unauthorized keys
            $filters = array_filter($request->query->all(), function($key) use($authorizedFilters) {
                return in_array($key, $authorizedFilters);
            }, ARRAY_FILTER_USE_KEY);

            // Sanitize data (todo : try better)
            array_walk($filters, function (&$value, $key) {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
            });
        }

        // Search results
        list($listDso, $listAggregates, $nbItems) = $dsoRepository->getObjectsCatalogByFilters($from, $filters);

        // List facets
        $allQueryParameters = $request->query->all();
        foreach ($listAggregates as $type => $listFacets) {
            $listAggregates[$type] = array_map(function($facet) use ($router, $allQueryParameters, $type, $translatorInterface) {
                return [
                    'value' => $translatorInterface->trans(sprintf('%s.%s', $type, strtolower(key($facet)))),
                    'number' => reset($facet),
                    'full_url' => $router->generate('dso_catalog', array_merge($allQueryParameters, [$type => key($facet)]))
                ];
            }, $listFacets);
        }

        // Params
        $result['list_dso'] = $dsoManager->buildListDso($listDso);
        $result['list_facets'] = $listAggregates;
        $result['nb_items'] = $nbItems;
        $result['current_page'] = $page;
        $result['nb_pages'] = ceil($nbItems/DsoRepository::SIZE);
        $result['filters'] = $filters;

        /** @var Response $response */
        $response = $this->render('pages/catalog.html.twig', $result);
        $response->setPublic();
        $response->setSharedMaxAge(0);

        return $response;
    }
}
