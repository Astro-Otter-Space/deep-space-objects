<?php

namespace App\ControllerApi;

use App\Classes\Utils;
use App\Controller\ControllerTraits\DsoTrait;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use Elastica\Exception\NotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class GeoJson
 *
 * @package App\Api\Controller
 */
final class DataController extends AbstractFOSRestController
{

    use DsoTrait, SymfonyServicesTrait;

    public const JSON_FORMAT = 'json';

    public const LIMIT = 20;

    private static array $authorizedTypes = [
        'constellation' => 'const_id',
        'catalog' => 'catalog',
        'type' => 'type'
    ];

    /**
     * @deprecated
     * @ Rest\Get("/dso/list/constellation/{constellation}", name="api_get_dso_by_constellation")
     *
     * @ Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @ Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     *
     * @param ParamFetcher $paramFetcher
     * @param string $constellation
     *
     * @ return View
     *
     * Doc : https://zestedesavoir.com/tutoriels/1280/creez-une-api-rest-avec-symfony-3/amelioration-de-lapi-rest/quand-utiliser-les-query-string/
     */
    /*public function getDsoByConstellation(ParamFetcher $paramFetcher, string $constellation): View
    {
        $offset = (int) $paramFetcher->get('offset') ?? null;
        $limit = (int) $paramFetcher->get('limit') ?? null;

        $params = ['constellation' => $constellation];
        if (!is_null($offset)) {
            $params['offset'] = $offset;
        }
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }

        return $this->routeRedirectView('api_get_dso_collection', $params, Response::HTTP_MOVED_PERMANENTLY);
    }*/


    /**
     * @deprecated
     * @param ParamFetcherInterface $paramFetcher
     * @param string $catalog
     *
     * @ return View
     * @ Rest\Get("/dso/list/catalog/{catalog}", name="api_get_dso_by_catalog")
     * @ Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @ Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
//    public function getDsoByCatalog(ParamFetcherInterface $paramFetcher, string $catalog): View
//    {
//        if (!in_array($catalog, Utils::getOrderCatalog(), true)) {
//            throw new InvalidParameterException("Parameter \"$catalog\" for catalog does not exist");
//        }
//
//        $offset = (int)$paramFetcher->get('offset') ?? null;
//        $limit = (int)$paramFetcher->get('limit') ?? null;
//
//        $params = ['catalog' => $catalog];
//        if (!is_null($offset)) {
//            $params['offset'] = $offset;
//        }
//        if (!is_null($limit)) {
//            $params['limit'] = $limit;
//        }
//
//        return $this->routeRedirectView('api_get_dso_collection', $params, Response::HTTP_MOVED_PERMANENTLY);
//    }


    /**
     * @deprecated
     * @param ParamFetcherInterface $paramFetcher
     * @param string $type
     *
     * @return View
     *
     * @ Rest\Get("/dso/list/type/{type}", name="api_get_dso_by_type")
     * @ Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @ Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
//    public function getDsoByType(ParamFetcherInterface $paramFetcher, string $type): View
//    {
//        if (!in_array($type, Utils::getListTypeDso(), true)) {
//            throw new InvalidParameterException("Parameter \"$type\" for type does not exist");
//        }
//
//        $offset = (int)$paramFetcher->get('offset');
//        $limit = (int)$paramFetcher->get('limit');
//
//        $params = ['type' => $type];
//        if (!is_null($offset)) {
//            $params['offset'] = $offset;
//        }
//        if (!is_null($limit)) {
//            $params['limit'] = $limit;
//        }
//        return $this->routeRedirectView('api_get_dso_collection', $params, Response::HTTP_MOVED_PERMANENTLY);
//    }
}
