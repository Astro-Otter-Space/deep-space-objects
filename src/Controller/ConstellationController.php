<?php

namespace App\Controller;

use App\Controller\ControllerTraits\DsoTrait;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Constellation;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
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
    private $translatorInterface;

    /**
     * ConstellationController constructor.
     *
     * @param ConstellationManager $constellationManager
     * @param DsoManager $dsoManager
     * @param DsoRepository $dsoRepository
     */
    public function __construct(ConstellationManager $constellationManager, DsoManager $dsoManager, DsoRepository $dsoRepository)
    {
        $this->constellationManager = $constellationManager;
        $this->dsoManager = $dsoManager;
        $this->dsoRepository = $dsoRepository;
    }


    /**
     * @Route("/constellation/{id}/{name}", name="constellation_show")
     *
     * @param string $id
     * @param string $name
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function show(string $id, string $name): Response
    {
        $result = [];

        /** @var Router $router */
        $router = $this->get('router');

        /** @var Serializer $serializer */
        $serializer = $this->container->get('serializer');

        /** @var ConstellationDTO $constellation */
        $constellation = $this->constellationManager->buildConstellation($id);

        // Retrieve list of Dso from the constellation
        /** @var ListDso $listDso */
        $listDso = $this->dsoRepository->getObjectsByConstId($constellation->getId(), null, DsoRepository::FROM, 300);
        $listDsoLimited = $this->dsoRepository->getObjectsByConstId($constellation->getId(), null, DsoRepository::FROM, DsoRepository::SMALL_SIZE);

        $constellation->setListDso($listDso);
        $result['list_dso'] = $this->dsoManager->buildListDso($listDsoLimited) ?? [];

        // Filter for Grid Cards dso
        $result['list_types_filters'] = $this->buildFiltersWithAll($listDsoLimited) ?? [];

        // List types of DSO for map legend
        $result['list_types'] = array_merge(...array_map(static function ($data) {
            return [$data['value'] => $data['label']];
        }, $this->buildFilters($listDso)));

        // GeoJson for display dso on map
        $listDsoFeatures = array_map(static function(DTOInterface $dso) {
            return $dso->getGeometry();
        }, iterator_to_array($constellation->getListDso()->getIterator()));

        $geoJsonDso = [
            "type" => "FeatureCollection",
            "features" => array_filter($listDsoFeatures)
        ];

        // Serialize Collection entity
        $result['constellation'] = $serializer->serialize($constellation, 'json');

        // Link to download map
        $result['link_download'] = $router->generate('download_map', ['id' => $constellation->getId()]);
        $result['geojsonDso'] = $geoJsonDso ?? null;
        $result['centerMap'] = $constellation->getGeometry()['coordinates'];
        $result['ajax_dso_by_const'] = $router->generate('get_dso_by_const_ajax', ['constId' => $constellation->getId()]);
        $result['breadcrumbs'] = $this->buildBreadcrumbs($constellation, $router, $constellation->getAlt());

        /** @var Response $response */
        $response = $this->render('pages/constellation.html.twig', $result);
        $response->headers->set('X-Constellation-Id', $constellation->getElasticId());
        $response->setSharedMaxAge(LayoutController::HTTP_TTL)->setPublic();

        return $response;
    }

    /**
     * @Route("/_get_dso_by_constellation/{constId}", name="get_dso_by_const_ajax")
     * @param Request $request
     * @param $constId
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    public function dsoByConstellationAjax(Request $request, $constId)
    {
        $offset = $request->query->get('offset');

        /** @var ListDso $listDso */
        $listDso = $this->dsoRepository->getObjectsByConstId($constId, null, $offset, DsoRepository::SMALL_SIZE);
        $listDsoCards = $this->dsoManager->buildListDso($listDso) ?? [];
        $result['dso'] = $listDsoCards ?? [];

        $listDsoAll = $this->dsoRepository->getObjectsByConstId($constId, null, 0, $offset+DsoRepository::SMALL_SIZE);
        $result['filters'] = $this->buildFiltersWithAll($listDsoAll) ?? [];

        return new JsonResponse($result);
    }

    /**
     * @Route("/constellations", name="constellation_list")
     *
     * @return Response
     * @throws \ReflectionException
     */
    public function list(): Response
    {
        $result = [];

        $result['list_constellation'] = $this->constellationManager->buildListConstellation();

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
