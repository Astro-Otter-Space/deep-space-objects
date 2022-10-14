<?php

namespace App\ControllerApi;

use App\Classes\Utils;
use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\DTOInterface;
use App\Entity\DTO\DsoDTO;
use App\Managers\DsoManager;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
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

    private DsoRepository $dsoRepository;
    private ConstellationRepository $constellationRepository;
    private DsoDataTransformer $dsoDataTransformer;
    private DsoManager $dsoManager;

    /**
     * DataController constructor.
     *
     * @param DsoManager $dsoManager
     * @param DsoRepository $dsoRepository
     * @param DsoDataTransformer $dsoDataTransformer
     */
    public function __construct(DsoManager $dsoManager, DsoRepository $dsoRepository, DsoDataTransformer $dsoDataTransformer)
    {
        $this->dsoRepository = $dsoRepository;
        $this->dsoDataTransformer = $dsoDataTransformer;
        $this->dsoManager = $dsoManager;
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
        /** @var DTOInterface $dso */
        $dso = $this->dsoManager->getDso($id);

        if (is_null($dso)) {
            throw new NotFoundException(sprintf("%s is not an correct item", $id));
        }

        $codeHttp = Response::HTTP_OK;

        /** @var DsoDTO|null $data */
        $data = $this->dsoDataTransformer->longView($dso);

        $formatedData = $this->buildJsonApi($data, $codeHttp);

        $view = $this->view($formatedData, $codeHttp);
        $view->setFormat(self::JSON_FORMAT);

        return $this->handleView($view);
    }

    /**
     * @param ParamFetcher $paramFetcher
     *
     * @return Response
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
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

        $constellation = ("" !== $paramFetcher->get('constellation')) ? $paramFetcher->get('constellation') : null;
        if (!is_null($constellation)) {
            $filters['constellation'] = $constellation;
        }

        $catalog = ("" !== $paramFetcher->get('catalog')) ? $paramFetcher->get('catalog') : null;
        if (!is_null($catalog)) {
            if (in_array($catalog, Utils::getOrderCatalog(), true)) {
                $filters['catalog'] = $catalog;
            } else {
                throw new InvalidParameterException("Parameter \"$catalog\" for catalog does not exist");
            }
        }

        $type = ("" !== $paramFetcher->get('type')) ? $paramFetcher->get('type') : null;
        if (!is_null($type)) {
            if (in_array($type, Utils::getListTypeDso(), true)) {
                $filters['type'] = $type;
            } else {
                throw new InvalidParameterException("Parameter \"$type\" for type does not exist");
            }
        }

        array_walk($filters, static function (&$value, $key) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        });

        [$listDsoId, ,] = $this->dsoRepository
            ->setLocale('en')
            ->getObjectsCatalogByFilters($offset, $filters, $limit, true);

        $listDso = $this->dsoManager->buildListDso($listDsoId);

        /** @var array $listDso */
        $listDsoView = $this->dsoDataTransformer->listVignettesView($listDso);

        $formatedData = $this->buildJsonApi($listDsoView, Response::HTTP_OK);

        $view = $this->view($formatedData, Response::HTTP_OK);
        $view->setFormat(self::JSON_FORMAT);

        return $this->handleView($view);
    }


    /**
     * @Rest\Get("/dso/by_constellation/{constellation}", name="api_objects_by_constellation")
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
        $offset = (int) $paramFetcher->get('offset') ?? null;
        $limit = (int) $paramFetcher->get('limit') ?? null;

        $params = ['constellation' => $constellation];
        if (!is_null($offset)) {
            $params['offset'] = $offset;
        }
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }

        return $this->routeRedirectView('api_dso_get_items', $params, Response::HTTP_MOVED_PERMANENTLY);
    }


    /**
     * @param ParamFetcherInterface $paramFetcher
     * @param string $catalog
     *
     * @return View
     * @Rest\Get("/dso/by_catalog/{catalog}", name="api_objects_by_catalog")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
    public function getDsoByCatalog(ParamFetcherInterface $paramFetcher, string $catalog): View
    {
        if (!in_array($catalog, Utils::getOrderCatalog(), true)) {
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
     * @param ParamFetcherInterface $paramFetcher
     * @param string $type
     *
     * @return View
     *
     * @Rest\Get("/dso/by_type/{type}", name="api_objects_by_type")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     */
    public function getDsoByType(ParamFetcherInterface $paramFetcher, string $type): View
    {
        if (!in_array($type, Utils::getListTypeDso(), true)) {
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
