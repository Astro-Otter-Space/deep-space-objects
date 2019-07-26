<?php

namespace App\ControllerApi;

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
     * @Rest\Get("/object/{id}", name="api_object_dso")
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
     * @Rest\Get("/objects/by_constellation/{constId}", name="api_objects_by_constellation")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="", description="Index start pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="20", description="Index end pagination")
     *
     * @param ParamFetcher $paramFetcher
     * @param string $constId
     *
     * @return Response
     * @throws \ReflectionException
     *
     * Doc : https://zestedesavoir.com/tutoriels/1280/creez-une-api-rest-avec-symfony-3/amelioration-de-lapi-rest/quand-utiliser-les-query-string/
     */
    public function getDsoByConstellation(ParamFetcher $paramFetcher, string $constId): Response
    {
        $offset = (int)$paramFetcher->get('offset');
        $limit = (int)$paramFetcher->get('limit');

        $listDsoData = $this->dsoRepository->getObjectsByConstId($constId, null, $offset, $limit, false);

        /** @var ListDso $listDso */
        $listDso = array_map(function(Document $document) {
            return $document->getData();
        }, $listDsoData);

        $formatedData = $this->buildJsonApi($listDso, Response::HTTP_OK);

        $view = $this->view($formatedData, Response::HTTP_OK);
        $view->setFormat(self::JSON_FORMAT);

        return $this->handleView($view);
    }

}
