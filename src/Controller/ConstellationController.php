<?php

namespace App\Controller;

use App\Controller\ControllerTraits\DsoTrait;
use App\DataTransformer\ConstellationDataTransformer;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\ListDso;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConstellationController
 * @package App\Controller
 */
class ConstellationController extends AbstractController
{
    use DsoTrait;

    /** @var ConstellationManager  */
    private $constellationManager;
    /** @var DsoManager  */
    private $dsoManager;
    /** @var DsoRepository  */
    private $dsoRepository;
    /** @var TranslatorInterface  */
    private $translator;

    /**
     * ConstellationController constructor.
     *
     * @param ConstellationManager $constellationManager
     * @param DsoManager $dsoManager
     * @param DsoRepository $dsoRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(ConstellationManager $constellationManager, DsoManager $dsoManager, DsoRepository $dsoRepository, TranslatorInterface $translator)
    {
        $this->constellationManager = $constellationManager;
        $this->dsoManager = $dsoManager;
        $this->dsoRepository = $dsoRepository;
        $this->translator = $translator;
    }


    /**
     * @Route("/constellation/{id}/{name}", name="constellation_show")
     *
     * @param string $id
     * @param string $name
     * @param DsoManager $dsoManager
     * @param DsoDataTransformer $dsoDataTransformer
     *
     * @return Response
     * @throws WsException
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function show(string $id, string $name, DsoManager $dsoManager, DsoDataTransformer $dsoDataTransformer): Response
    {
        $result = [];

        /** @var Router $router */
        $router = $this->get('router');

        /** @var Serializer $serializer */
        $serializer = $this->container->get('serializer');

        /** @var ConstellationDTO $constellation */
        $constellation = $this->constellationManager->buildConstellation($id);

        $listDso = $dsoManager->getListDsoFromConst($constellation->getId(), null, 0,DsoRepository::SMALL_SIZE);
        $result['list_dso'] = $listDsoCards = $dsoDataTransformer->listVignettesView($listDso);

        // Filter for Grid Cards dso
        $result['list_types_filters'] = $this->buildFiltersWithAll($listDso) ?? [];

        // List types of DSO for map legend
        $result['list_types'] = array_merge(...array_map(static function ($data) {
            return [$data['value'] => $data['label']];
        }, $this->buildFilters($listDso)));

        // GeoJson for display dso on map
        $listDsoFeatures = array_map(static function(DTOInterface $dso) {
            return $dso->getGeometry();
        }, iterator_to_array($listDso));

        $geoJsonDso = [
            "type" => "FeatureCollection",
            "features" => array_filter($listDsoFeatures)
        ];

        // Serialize Collection entity
        $result['constellation'] = $constellation; //$serializer->serialize($constellation, 'json');
        $result['title'] = $constellation->title();

        // Link to download map
        $result['link_download'] = $router->generate('download_map', ['id' => $constellation->getId()]);
        $result['geojsonDso'] = $geoJsonDso ?? null;
        $result['centerMap'] = $constellation->getGeometry()['coordinates'];
        $result['ajax_dso_by_const'] = $router->generate('get_dso_by_const_ajax', ['constId' => $constellation->getId()]);
        $result['breadcrumbs'] = $this->buildBreadcrumbs($constellation, $router, $constellation->title());

        /** @var Response $response */
        $response = $this->render('pages/constellation.html.twig', $result);
        $response->headers->set('X-Constellation-Id', $constellation->getElasticSearchId());
        $response->setSharedMaxAge(LayoutController::HTTP_TTL)->setPublic();

        return $response;
    }

    /**
     * @Route("/_get_dso_by_constellation/{constId}", name="get_dso_by_const_ajax")
     * @param Request $request
     * @param string $constId
     * @param DsoDataTransformer $dsoDataTransformer
     *
     * @return JsonResponse
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function dsoByConstellationAjax(Request $request, string $constId, DsoDataTransformer $dsoDataTransformer): JsonResponse
    {
        $offset = $request->query->get('offset');

        $listDso = $dsoDataTransformer->listVignettesView($this->dsoManager->getListDsoFromConst($constId, null,  $offset, DsoRepository::SMALL_SIZE));
        $listDsoAll = $this->dsoManager->getListDsoFromConst($constId, null, 0, DsoRepository::SMALL_SIZE);

        $result['dso'] = $listDso;
        $result['filters'] = $this->buildFiltersWithAll($listDsoAll);

        return new JsonResponse($result);
    }

    /**
     * @Route("/constellations", name="constellation_list")
     *
     * @param Request $request
     * @param ConstellationDataTransformer $constellationDataTransformer
     *
     * @return Response
     */
    public function list(Request $request, ConstellationDataTransformer $constellationDataTransformer): Response
    {
        $result = [];

        $listConstellations = $this->constellationManager->buildListConstellation();

        $result['list_constellation'] = $constellationDataTransformer->listVignettesView($listConstellations);

        /** @var Response $response */
        $response = $this->render('pages/constellations.html.twig', $result);
        $response->setSharedMaxAge(LayoutController::HTTP_TTL)->setPublic();

        return $response;
    }


    /**
     * @Route("/download/map/{id}",name="download_map")
     * Download map constellation
     * @param string $id
     * @return BinaryFileResponse|NotFoundHttpException
     */
    public function getMapConstellation(string $id): BinaryFileResponse
    {
        $webPath = $this->getParameter('kernel.project_dir') . '/public/';

        $file = $webPath . sprintf('build/images/const_maps/%s.gif', strtoupper($id));

        if (!file_exists($file)) {
            return new NotFoundHttpException();
        }

        /** @var FileinfoMimeTypeGuesser $typeMimeGuesser */
        $typeMimeGuesser = new FileinfoMimeTypeGuesser();

        /** @var BinaryFileResponse $response */
        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', $typeMimeGuesser->guessMimeType($file));

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($file)
        );

        return $response;
    }

}
