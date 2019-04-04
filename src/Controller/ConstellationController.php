<?php

namespace App\Controller;

use App\Entity\Constellation;
use App\Entity\Dso;
use App\Entity\ListDso;
use App\Helpers\UrlGenerateHelper;
use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ConstellationController
 * @package App\Controller
 */
class ConstellationController extends AbstractController
{

    /**
     * @Route("/constellation/{id}", name="constellation_show")
     *
     * @param string $id
     * @param ConstellationManager $constellationManager
     * @param DsoRepository $dsoRepository
     * @param DsoManager $dsoManager
     * @return Response
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    public function show(string $id, ConstellationManager $constellationManager, DsoRepository $dsoRepository, DsoManager $dsoManager): Response
    {
        $result = [];

        /** @var Router $router */
        $router = $this->get('router');

        /** @var Serializer $serializer */
        $serializer = $this->container->get('serializer');

        /** @var Constellation $constellation */
        $constellation = $constellationManager->buildConstellation($id);

        // Retrieve list of Dso from the constellation
        /** @var ListDso $listDso */
        $listDso = $dsoRepository->getObjectsByConstId($constellation->getId(), null,20);

        $constellation->setListDso($listDso);
        $result['list_dso'] = $dsoManager->buildListDso($constellation->getListDso());


        $geoJsonDso =[
            "type" => "FeatureCollection",
            "features" => array_map(function(Dso $dso) use($dsoManager) {
                                return $dsoManager->buildgeoJson($dso);
                            }, iterator_to_array($constellation->getListDso()->getIterator()) )
        ];

        // Serialize Collection entity
        $result['constellation'] = $serializer->serialize($constellation, 'json');

        // Link to download map
        $result['link_download'] = $router->generate('download_map', ['id' => $constellation->getId()]);
        $result['geojsonDso'] = $geoJsonDso;
        $result['centerMap'] = $constellation->getGeometry()['coordinates'];

        /** @var Response $response */
        $response = $this->render('pages/constellation.html.twig', $result);
        $response->headers->set('X-Constellation-Id', $constellation->getElasticId());
        $response->setPublic();

        return $response;
    }


    /**
     *
     * @Route("/constellations", name="constellation_list")
     * @param ConstellationManager $constellationManager
     * @return Response
     * @throws \ReflectionException
     */
    public function list(ConstellationManager $constellationManager): Response
    {
        $result = [];

        $listConstellation = $constellationManager->buildListConstellation();
        $result['list_constellation'] = $listConstellation;

        /** @var Response $response */
        $response = $this->render('pages/constellations.html.twig', $result);
        $response->setPublic();

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
        $response->headers->set('Content-Type', $typeMimeGuesser->guess($file));

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($file)
        );

        return $response;
    }

}
