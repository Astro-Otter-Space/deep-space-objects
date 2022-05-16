<?php

declare(strict_types=1);

namespace App\Controller\Constellation;

use App\Controller\ControllerTraits\DsoTrait;
use App\Controller\LayoutController;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Repository\AbstractRepository;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
final class Show extends AbstractController
{

    use SymfonyServicesTrait, DsoTrait;

    /**
     * @Route("/constellation/{id}/{name}", name="constellation_show")
     *
     * @param Request $request
     * @param string $id
     * @param string $name
     * @param DsoManager $dsoManager
     * @param DsoDataTransformer $dsoDataTransformer
     * @param ConstellationManager $constellationManager
     *
     * @return Response
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(
        Request $request,
        string $id,
        string $name,
        DsoManager $dsoManager,
        DsoDataTransformer $dsoDataTransformer,
        ConstellationManager $constellationManager
    ): Response
    {
        $result = [];
        /** @var ConstellationDTO $constellation */
        $constellation = $constellationManager->getConstellationById($id);

        $listDso = $dsoManager->getListDsoFromConst($constellation->getId(), null, 0, AbstractRepository::SMALL_SIZE);
        $result['list_dso'] = $listDsoCards = $dsoDataTransformer->listVignettesView($listDso);

        // Filter for Grid Cards dso
        $result['list_types_filters'] = $this->buildFiltersWithAll($listDso) ?? [];

        // List types of DSO for map legend
        $result['list_types'] = array_merge(...array_map(static function ($data) {
            return [$data['value'] => $data['label']];
        }, $this->buildFilters($listDso)));

        // GeoJson for display dso on map
        $listDsoFeatures = array_map(static function(DTOInterface $dso) {
            return $dso->geoJson();
        }, iterator_to_array($listDso));

        $geoJsonDso = [
            "type" => "FeatureCollection",
            "features" => array_filter($listDsoFeatures)
        ];

        // Serialize Collection entity
        $result['constellation'] = $constellation;
        $result['title'] = $constellation->title();

        // Link to download map
        $result['link_download'] = $this->router->generate('download_map', ['id' => $constellation->getId()]);
        $result['geojsonDso'] = $geoJsonDso ?? null;
        $result['centerMap'] = $constellation->getGeometry()['coordinates'];
        $result['ajax_dso_by_const'] = $this->router->generate('get_dso_by_const_ajax', ['constId' => $constellation->getId()]);
        $result['breadcrumbs'] = $this->buildBreadcrumbs($constellation, $this->router, $constellation->title());

        $response = $this->render('pages/constellation.html.twig', $result);
        $response->headers->set('X-Constellation-Id', $constellation->getElasticSearchId());
        $response->setSharedMaxAge(LayoutController::HTTP_TTL)->setPublic();

        return $response;
    }
}
