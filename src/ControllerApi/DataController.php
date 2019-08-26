<?php

namespace App\ControllerApi;

use App\Classes\Utils;
use App\Controller\ControllerTraits\DsoTrait;
use App\Entity\Dso;
use App\Entity\ListDso;
use App\Managers\DsoManager;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use Elastica\Document;
use Elastica\Exception\NotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\ConfigurableViewHandlerInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;

/**
 * Class DsoController
 *
 * @package App\Api\Controller
 */
final class DataController extends AbstractFOSRestController
{

    use DsoTrait;

    const JSON_FORMAT = 'json';

    const LIMIT = 20;

    private static $authorizedTypes = [
        'constellation' => 'const_id',
        'catalog' => 'catalog',
        'type' => 'type'
    ];

    /** @var DsoRepository  */
    private $dsoRepository;
    /** @var ConstellationRepository */
    private $constellationRepository;

    /**
     * DataController constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param ConstellationRepository $constellationRepository
     */
    public function __construct(DsoRepository $dsoRepository, ConstellationRepository $constellationRepository)
    {
        $this->dsoRepository = $dsoRepository;
        $this->constellationRepository = $constellationRepository;
    }


    /**
     * @Rest\Get("/dso/id/{id}", name="api_object_dso")
     *
     * @param string $id
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function getDso(string $id): Response
    {
        /** @var Document $dso */
        $dso = $this->dsoRepository->getObjectById($id, false);

        if (is_null($dso)) {
            throw new NotFoundException(sprintf("%s is not an correct item", $id));
        } else {
            $codeHttp = Response::HTTP_OK;
            $data = $dso->getData();
        }

        $formatedData = $this->buildJsonApi($data, $codeHttp);

        $view = $this->view($formatedData, $codeHttp);
        $view->setFormat(self::JSON_FORMAT);

        return $this->handleView($view);
    }

    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     * @throws \ReflectionException
     *
     * @Rest\Get("/dso/get_objects_by", name="api_dso_get_items")
     *
     * @Rest\QueryParam(name="constellation", requirements="\w+", default="")
     * @Rest\QueryParam(name="catalog", requirements="\w+", default="")
     * @Rest\QueryParam(name="type", requirements="\w+",default="")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
    public function getObjectsByFilters(ParamFetcher $paramFetcher): Response
    {
        $filters = [];

        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $constellation = ("" != $paramFetcher->get('constellation')) ? $paramFetcher->get('constellation') : null;
        if (!is_null($constellation)) {
            $filters['constellation'] = $constellation;
        }

        $catalog = ("" != $paramFetcher->get('catalog')) ? $paramFetcher->get('catalog') : null;
        if (!is_null($catalog)) {
            if (in_array($catalog, Utils::getOrderCatalog())) {
                $filters['catalog'] = $catalog;
            } else {
                throw new InvalidParameterException("Parameter \"$catalog\" for catalog does not exist");
            }
        }

        $type = ("" != $paramFetcher->get('type')) ? $paramFetcher->get('type') : null;
        if (!is_null($type)) {
            if (in_array($type, Utils::getListTypeDso())) {
                $filters['type'] = $type;
            } else {
                throw new InvalidParameterException("Parameter \"$type\" for type does not exist");
            }
        }

        array_walk($filters, function (&$value, $key) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        });

        /**  */
        list($listDsoData, ) = $this->dsoRepository->getObjectsCatalogByFilters($offset, $filters, $limit, false);

        /** @var ListDso $listDso */
        $listDso = array_map(function(Document $document) {
            return $document->getData();
        }, $listDsoData);

        $formatedData = $this->buildJsonApi($listDso, Response::HTTP_OK);

        $view = $this->view($formatedData, Response::HTTP_OK);
        $view->setFormat(self::JSON_FORMAT);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/objects/by_constellation/{constellation}", name="api_objects_by_constellation")
     *
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     *
     * @param ParamFetcher $paramFetcher
     * @param string $constellation
     *
     * @return View
     *
     * Doc : https://zestedesavoir.com/tutoriels/1280/creez-une-api-rest-avec-symfony-3/amelioration-de-lapi-rest/quand-utiliser-les-query-string/
     */
    public function getDsoByConstellation(ParamFetcher $paramFetcher, string $constellation): View
    {
        $offset = (int)$paramFetcher->get('offset') ?? null;
        $limit = (int)$paramFetcher->get('limit') ?? null;

        $params = ['constellation' => $constellation];
        if (!is_null($offset)) {
            $params['offset'] = $offset;
        }
        if (!is_null($limit) && isset($limit)) {
            $params['limit'] = $limit;
        }

        return $this->routeRedirectView('api_dso_get_items', $params, Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param string $catalog
     *
     * @return View
     * @Rest\Get("/dso/by_catalog/{catalog}", name="api_objects_by_catalog")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
    public function getDsoByCatalog(ParamFetcher $paramFetcher, string $catalog): View
    {
        if (!in_array($catalog, Utils::getOrderCatalog())) {
            throw new InvalidParameterException("Parameter \"$catalog\" for catalog does not exist");
        }

        $offset = (int)$paramFetcher->get('offset') ?? null;
        $limit = (int)$paramFetcher->get('limit') ?? null;

        $params = ['catalog' => $catalog];
        if (!is_null($offset)) {
            $params['offset'] = $offset;
        }
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }

        return $this->routeRedirectView('api_dso_get_items', $params, Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param string $type
     *
     * @return View
     *
     * @Rest\Get("/dso/by_type/{type}", name="api_objects_by_type")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
    public function getDsoByType(ParamFetcher $paramFetcher, string $type): View
    {
        if (!in_array($type, Utils::getListTypeDso())) {
            throw new InvalidParameterException("Parameter \"$type\" for type does not exist");
        }

        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $params = ['type' => $type];
        if (!is_null($offset)) {
            $params['offset'] = $offset;
        }
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }
        return $this->routeRedirectView('api_dso_get_items', $params, Response::HTTP_MOVED_PERMANENTLY);
    }
}
