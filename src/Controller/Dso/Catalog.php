<?php

declare(strict_types=1);

namespace App\Controller\Dso;

use App\Classes\Utils;
use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Managers\DsoManager;
use App\Repository\AbstractRepository;
use App\Repository\DsoRepository;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class Catalog extends AbstractController
{
    public const DEFAULT_PAGE = 1;

    use DsoTrait, SymfonyServicesTrait;

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
     * @param DsoManager $dsoManager
     * @param DsoRepository $dsoRepository
     * @param DsoDataTransformer $dsoDataTransformer
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        DsoManager $dsoManager,
        DsoRepository $dsoRepository,
        DsoDataTransformer $dsoDataTransformer
    ): Response
    {
        $page = self::DEFAULT_PAGE;
        $from = AbstractRepository::FROM;
        $filters = $listAggregations = [];
        $ordering = Utils::getOrderCatalog();

        if ($request->query->has('page')) {
            $page = (int)filter_var($request->query->get('page'), FILTER_SANITIZE_NUMBER_INT);
            $from = (AbstractRepository::SIZE)*($page-1);
        }

        if (0 < $request->query->count()) {
            $authorizedFilters = $dsoRepository->getListAggregates(true);

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
        [$listDsoId, $listAggregates, $nbItems] = $dsoRepository
            ->setLocale($request->getLocale())
            ->getObjectsCatalogByFilters($from, $filters, null, true);

        try {
            $listDso = $dsoManager->buildListDso($listDsoId);
        } catch (WsException|\JsonException|\ReflectionException $e) {
            $listDso = [];
        }

        // List facets
        $allQueryParameters = $request->query->all();
        foreach ($listAggregates as $type => $listFacets) {
            $typeTr = $this->translator->trans($type, ['%count%' => $listFacets['count']]);
            $listFacetsByType = array_map(function($facet) use ($allQueryParameters, $type) {
                return [
                    'code' => key($facet),
                    'value' => $this->translator->trans(sprintf('%s.%s', $type, strtolower(key($facet['name'])))),
                    'number' => reset($facet),
                    'full_url' => $this->router->generate('dso_catalog', array_merge($allQueryParameters, [$type => key($facet)]))
                ];
            }, $listFacets);

            $routeDelete = '';
            if (array_key_exists($type, $filters)) {
                $routeDelete = $this->router->generate(
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
        $result['list_dso'] = $dsoDataTransformer->listVignettesView($listDso);
        $result['list_facets'] = $listAggregations;
        $result['nb_items'] = (int)$nbItems;
        $result['current_page'] = $page;
        $result['nb_pages'] = $nbPages = ceil($nbItems/AbstractRepository::SIZE);

        $queryAll = $request->query->all();
        $result['filters'] = array_merge(array_map(function ($val, $key) use ( $queryAll) {
            return ['label' => $this->translator->trans(sprintf('%s.%s', $key, strtolower($val))), 'delete_url' => $this->router->generate('dso_catalog', array_diff_key($queryAll, [$key => $val]))];
        }, $filters, array_keys($filters)));

        unset($queryAll['page']);
        $result['pagination'] = [
            'prev' => (self::DEFAULT_PAGE < $page) ? $this->router->generate('dso_catalog', array_merge($queryAll, ['page' => $page-1])): null,
            'next' => ($nbPages > $page) ? $this->router->generate('dso_catalog', array_merge($queryAll, ['page' => $page+1])): null
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

        $result['download_link'] = $this->router->generate('download_data', $queryAll);

        $response = $this->render('pages/catalog.html.twig', $result);
        $response->setPublic();
        $response->setSharedMaxAge(0);

        return $response;
    }
}
