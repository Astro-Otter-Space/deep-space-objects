<?php

namespace App\Controller;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\BDD\UpdateData;
use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Response\Image;
use AstrobinWs\Response\ListImages;
use AstrobinWs\Services\GetImage;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Exception\NotFoundException;
use JsonException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoController
 * @package App\Controller
 */
class DsoController extends AbstractController
{
    public const DEFAULT_PAGE = 1;

    use DsoTrait;

    /** @var CacheInterface  */
    private $cacheUtil;
    /** @var DsoManager */
    private $dsoManager;
    /** @var DsoRepository */
    private $dsoRepository;
    /** @var TranslatorInterface */
    private $translator;
    /** @var DsoDataTransformer */
    private $dsoDataTransformer;

    /**
     * DsoController constructor.
     *
     * @param CacheInterface $cacheUtil
     * @param DsoManager $dsoManager
     * @param DsoRepository $dsoRepository
     * @param TranslatorInterface $translator
     * @param DsoDataTransformer $dataTransformer
     */
    public function __construct(CacheInterface $cacheUtil, DsoManager $dsoManager, DsoRepository $dsoRepository, TranslatorInterface $translator, DsoDataTransformer $dataTransformer)
    {
        $this->cacheUtil = $cacheUtil;
        $this->dsoManager = $dsoManager;
        $this->dsoRepository = $dsoRepository;
        $this->translator = $translator;
        $this->dsoDataTransformer = $dataTransformer;
    }

    /**
     * @Route({
     *  "en": "/catalog/{id}",
     *  "fr": "/catalogue/{id}",
     *  "es": "/catalogo/{id}",
     *  "pt": "/catalogo/{id}",
     *  "de": "/katalog/{id}"
     * }, name="dso_show")
     *
     * @param Request $request
     * @param string $id
     *
     * @return Response
     * @throws WsException
     * @throws ReflectionException
     * @throws JsonException
     */
    public function show(Request $request, string $id): Response
    {
        $separator = trim(Utils::URL_CONCAT_GLUE);
        $id = explode($separator, $id, null);
        $id = reset($id);

        $dso = $this->dsoManager->getDso($id);
        $params['desc'] = implode(Utils::GLUE_DASH, $dso->getDesigs());

        if (!is_null($dso)) {
            /** @var DsoDTO|DTOInterface */
            $params['dso'] = $dso;
            $params['dsoData'] = $this->dsoManager->formatVueData($dso);
            $params['constTitle'] = $dso->getConstellation()->title() ?? "";
            $params['last_update'] = $dso->getUpdatedAt();

            // Image cover
            $params['imgCoverAlt'] = ($dso->getAstrobin()->title) ? sprintf('"%s" by %s', $dso->getAstrobin()->title, $dso->getAstrobin()->user) : null;

            // List of Dso from same constellation
            $listDso = $this->dsoManager->getListDsoFromConst($dso->getConstellation()->getId(), $dso->getId(), 0, 20);

            $params['dso_by_const'] = $this->dsoDataTransformer->listVignettesView($listDso);
            $params['list_types_filters'] = $this->buildFiltersWithAll($listDso) ?? [];

            // Map
            $params['geojson_dso'] = [
                "type" => "FeatureCollection",
                "features" => [$dso->geoJson()]
            ];
            $params['geojson_center'] = $dso->getGeometry()['coordinates'];

            $params['images'] = [];
            // Images
            try {
                if ($this->cacheUtil->hasItem(md5($id . '_list_images'))) {
                    $params['images'] = unserialize($this->cacheUtil->getItem(md5($id . '_list_images')), ['allowed_classes' => false]);
                } else {
                    $params['images'] = $this->getListImages($dso->getName());
                }
            } catch (WsResponseException | JsonException | WsException | ReflectionException $e) {
                //dump($e->getMessage());
            }
        } else {
            throw new NotFoundException('Object not found');
        }

        $params['breadcrumbs'] = $this->buildBreadcrumbs($dso, $this->get('router'), $dso->title());

        $response = $this->render('pages/dso.html.twig', $params);

        // cache expiration
        $response->setPublic();
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        // cache validation
        $response->setLastModified($dso->getUpdatedAt());

        $listDsoIdHeaders = [
            md5(sprintf('%s_%s', $id, $request->getLocale())),
            md5(sprintf('%s_cover', $id))
        ];
        $response->headers->set('x-dso-id', implode(' ', $listDsoIdHeaders));

        return $response;
    }


    /**
     * Get last updated items
     *
     * @param Request $request
     *
     * @param EntityManagerInterface $doctrineManager
     *
     * @return Response
     * @throws ReflectionException|WsException
     * @throws JsonException
     * @Route({
     *   "en": "/last-update",
     *   "fr": "/mises-a-jour"
     * }, name="last_update_dso")
     */
    public function getLastUpdatedDso(Request $request, EntityManagerInterface $doctrineManager): Response
    {
        $listDso = $this->dsoManager->getListDsoLastUpdated();

        /** @var RouterInterface $router */
        $router = $this->get('router');

        /** @var UpdateData $lastUpdateData */
        $lastUpdateData = $doctrineManager->getRepository(UpdateData::class)->findOneBy([], ['date' => 'DESC']);

        $lastUpdateDate = $lastUpdateData->getDate()->format($this->translator->trans('dateFormatLong'));

        $title = $this->translator->trans('last_update_item', ['%date%' => $lastUpdateDate]);
        $titleBr = $this->translator->trans('last_update_title');
        $params = [
            'title' => $title,
            'breadcrumbs' => $this->buildBreadcrumbs(null, $router, $titleBr),
            'list_dso' => $this->dsoDataTransformer->listVignettesView($listDso)
        ];

        /** @var Response $response */
        $response = $this->render('pages/last_dso_updated.html.twig', $params);
        $response->setSharedMaxAge(0)
            ->setLastModified(null);

        return $response;
    }

    /**
     * Retrieve list of images for carousel
     *
     * @param string $dsoId
     *
     * @return null|array
     * @throws WsException
     * @throws ReflectionException
     * @throws JsonException
     */
    private function getListImages(string $dsoId): array
    {
        $astrobinWs = new GetImage();
        $listImages = $tabImages = [];
        try {
            /** @var ListImages|Image $listImages */
            $listImages = $astrobinWs->getImagesByTitle($dsoId, 5);
        } catch (WsResponseException | WsException $e) {
            //dump($e->getMessage());
        }

        if ($listImages instanceof Image) {
            $tabImages = $listImages->url_regular;

        } elseif ($listImages instanceof ListImages && 0 < $listImages->count) {
            $tabImages = array_map(static function (Image $image) {
                return $image->url_regular;
            }, iterator_to_array($listImages));
        }

        $this->cacheUtil->saveItem(md5($dsoId . '_list_images'), serialize($tabImages));
        return $tabImages;
    }

    /**
     * @Route("/geodata/dso/{id}", name="dso_geo_data", options={"expose": true})
     * @param string $id
     *
     * @return JsonResponse
     * @throws WsException
     * @throws ReflectionException|JsonException
     */
    public function geoJson(string $id): JsonResponse
    {
        $dso = $this->dsoManager->getDso($id);
        $geoJsonData = $dso->geoJson();

        /** @var JsonResponse $jsonResponse */
        $jsonResponse = new JsonResponse($geoJsonData, Response::HTTP_OK);
        $jsonResponse->setPublic();
        $jsonResponse->setSharedMaxAge(0);

        return $jsonResponse;
    }

    /**
     * @Route({
     *  "en": "/catalogs/{catalog}",
     *  "fr": "/catalogues/{catalog}",
     *  "es": "/catalogos/{catalog}",
     *  "pt": "/catalogos/{catalog}",
     *  "de": "/kataloge/{catalog}"
     * }, name="dso_catalog_redirect")
     *
     * @param Request $request
     * @param string|null $catalog
     *
     * @return RedirectResponse
     */
    public function catalogRedirect(Request $request, ?string $catalog): RedirectResponse
    {
        return $this->redirectToRoute('dso_catalog', ['catalog' => $catalog]);
    }


    /**
     * @Route({
     *  "en": "/catalogs",
     *  "fr": "/catalogues",
     *  "es": "/catalogos",
     *  "pt": "/catalogos",
     *  "de": "/kataloge"
     * }, name="dso_catalog")
     *
     * @param Request $request
     *
     * @return Response
     * @throws ReflectionException
     * @throws WsException
     */
    public function catalog(Request $request): Response
    {
        $page = self::DEFAULT_PAGE;
        $from = DsoRepository::FROM;
        $filters = $listAggregations = [];
        $ordering = Utils::getOrderCatalog();

        /** @var Router $router */
        $router = $this->get('router');

        if ($request->query->has('page')) {
            $page = (int)filter_var($request->query->get('page'), FILTER_SANITIZE_NUMBER_INT);
            if (is_int($page)) {
                $from = (DsoRepository::SIZE)*($page-1);
            }
        }

        if (0 < $request->query->count()) {
            $authorizedFilters = $this->dsoRepository->getListAggregates(true);

            // Removed unauthorized keys
            $filters = array_filter($request->query->all(), static function($key) use($authorizedFilters) {
                return in_array($key, $authorizedFilters, true);
            }, ARRAY_FILTER_USE_KEY);

            // Sanitize data (todo : try better)
            array_walk($filters, static function (&$value, $key) {
                $value = filter_var($value, FILTER_SANITIZE_STRING);
            });
        }

        // Search results
        [$listDsoId, $listAggregates, $nbItems] = $this->dsoRepository
            ->setLocale($request->getLocale())
            ->getObjectsCatalogByFilters($from, $filters, null, true);

        $listDso = $this->dsoManager->buildListDso($listDsoId);

        // List facets
        $allQueryParameters = $request->query->all();
        foreach ($listAggregates as $type => $listFacets) {
            $typeTr = $this->translator->trans($type, ['%count%' => count($listFacets)]);
            $listFacetsByType = array_map(function($facet) use ($router, $allQueryParameters, $type) {
                return [
                    'code' => key($facet),
                    'value' => $this->translator->trans(sprintf('%s.%s', $type, strtolower(key($facet)))),
                    'number' => reset($facet),
                    'full_url' => $router->generate('dso_catalog', array_merge($allQueryParameters, [$type => key($facet)]))
                ];
            }, $listFacets);

            $routeDelete = '';
            if (array_key_exists($type, $filters)) {
                $routeDelete = $router->generate(
                  'dso_catalog',
                    array_diff_key(
                        $request->query->all(),
                        [$type => $filters[$type]]
                    )
                );
            }


            // Sort here because dont know ho to do in aggregates query...
            // Specific sort for catalog
            if ('catalog' === $type) {
                usort($listFacetsByType, static function($facetA, $facetB) use ($ordering) {
                    return (array_search($facetA['code'], $ordering, true) > array_search($facetB['code'], $ordering, true));
                });
            } elseif ('constellation' === $type) {
                usort($listFacetsByType, static function($kFacetA, $kFacetB) {
                    return strcmp($kFacetA['code'], $kFacetB['code']);
                });
            }

            $listAggregations[$type] = [
                'name' => $typeTr,
                'delete_url' => $routeDelete,
                'list' => $listFacetsByType
            ];
        }

        // Params
        $result['list_dso'] = $this->dsoDataTransformer->listVignettesView($listDso);
        $result['list_facets'] = $listAggregations;
        $result['nb_items'] = (int)$nbItems;
        $result['current_page'] = $page;
        $result['nb_pages'] = $nbPages = ceil($nbItems/DsoRepository::SIZE);

        $queryAll = $request->query->all();
        $result['filters'] = array_merge(array_map(function ($val, $key) use ($router, $queryAll) {
            return ['label' => $this->translator->trans(sprintf('%s.%s', $key, strtolower($val))), 'delete_url' => $router->generate('dso_catalog', array_diff_key($queryAll, [$key => $val]))];
        }, $filters, array_keys($filters)));

        unset($queryAll['page']);
        $result['pagination'] = [
          'prev' => (self::DEFAULT_PAGE < $page) ? $router->generate('dso_catalog', array_merge($queryAll, ['page' => $page-1])): null,
          'next' => ($nbPages > $page) ? $router->generate('dso_catalog', array_merge($queryAll, ['page' => $page+1])): null
        ];

        // Description
        $result['pageDesc'] = $this->translator->trans('filteringList');
        if ($request->query->has('catalog')) {
            $catalog = $request->query->get('catalog');
            $desc = $this->translator->trans('description.' . $catalog);
            if (!empty($desc) && $desc !== 'description.' . $catalog) {
                $result['pageDesc'] = $desc;
            }
        }

        $result['download_link'] = $router->generate('download_data', $queryAll);

        /** @var Response $response */
        $response = $this->render('pages/catalog.html.twig', $result);
        $response->setPublic();
        $response->setSharedMaxAge(0);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @param int $offset
     *
     * @return Response
     * @throws WsException
     * @throws ReflectionException
     * @throws JsonException
     * @Route("/debug-astrobin/{offset}", name="debug_astrobin")
     */
    public function debugAstrobinImage(Request $request, $offset = 0): Response
    {
        $items = $this->dsoRepository->getAstrobinId(null);
        ksort($items);
        $items = array_slice($items, $offset, 50);
        $listDso = $this->dsoManager->buildListDso(array_keys($items));
        $params['dso'] = $this->dsoDataTransformer->listVignettesView($listDso);

        return $this->render('pages/debug_astrobin.html.twig', $params);
    }
}
