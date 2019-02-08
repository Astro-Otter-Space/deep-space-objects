<?php


namespace App\Controller;

use App\Entity\Dso;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Response\Image;
use Astrobin\Response\ListImages;
use Astrobin\Services\GetImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class DsoController
 * @package App\Controller
 */
class DsoController extends AbstractController
{

    /**
     * @Route({
     *  "en": "/catalog/{id}",
     *  "fr": "/catalogue/{id}",
     *  "es": "/catalogo/{id}",
     *  "pt": "/catalogo/{id}",
     *  "de": "/katalog/{id}"
     * }, name="dso_show")
     *
     * @param string $id
     * @param DsoManager $dsoManager
     * @return Response
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function show(string $id, DsoManager $dsoManager)
    {
        $params = [];

        /** @var Dso $dso */
        $dso = $dsoManager->buildDso($id);
        if (!is_null($dso)) {
            $params['dso'] = $dsoManager->formatVueData($dso);
            $params['constTitle'] = $dsoManager->buildTitleConstellation($dso->getConstId());
            $params['title'] = $dsoManager->buildTitle($dso);
            $params['imgCover'] = $dso->getImage();
            $params['geojsonDso'] = $dsoManager->buildgeoJson($dso);

            $params['dso_by_const'] = $dsoManager->buildListDso($dso, 20);
            $params['images'] = [];
            try {
                /** @var GetImage $astrobinWs */
                $astrobinWs = new GetImage();
                /** @var ListImages $listImages */
                $listImages = $astrobinWs->getImagesBySubject($dso->getId(), 5);
                if (0 < $listImages->count) {
                    $params['images'] = array_map(function (Image $image) {
                        return $image->url_regular;
                    }, iterator_to_array($listImages));
                }
            } catch(WsResponseException $e) {
//                dump($e->getMessage());
            }
        }

        /** @var Response $response */
        $response = $this->render('pages/dso.html.twig', $params);
        $response->setPublic();
        $response->setSharedMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set('x-dso-id', $dso->getElasticId());

        return $response;
    }


    /**
     * @Route("/geodata/dso/{id}", name="dso_geo_data", options={"expose": true})
     * @param string $id
     * @param DsoManager $dsoManager
     * @return JsonResponse
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function geoJson(string $id, DsoManager $dsoManager)
    {
        /** @var Dso $dso */
        $dso = $dsoManager->buildDso($id);

        $geoJsonData = $dsoManager->buildgeoJson($dso);

        /** @var JsonResponse $jsonResponse */
        $jsonResponse = new JsonResponse($geoJsonData, Response::HTTP_OK);
        $jsonResponse->setPublic();
        $jsonResponse->setSharedMaxAge(0);

        return $jsonResponse;
    }


    /**
     * @Route({
     *  "en": "/catalog",
     *  "fr": "/catalogue",
     *  "es": "/catalogo",
     *  "pt": "/catalogo",
     *  "de": "/katalog"
     * }, name="dso_catalog")
     * @return Response
     */
    public function catalog(Request $request, DsoRepository $dsoRepository)
    {
        $page = 1;
        $from = DsoRepository::FROM;
        $filters = [];

        if ($request->request->all()) {
            // retrieve search parameters

            if ($request->request->has('page')) {
                $page = $request->request->get('page');
                $from = DsoRepository::SIZE * ($page-1);
            }
        }

        $items = $dsoRepository->getObjectsCatalogByFilters($from, $filters);

        /** @var Response $response */
        $response = $this->render('pages/catalog.html.twig');

        return $response;
    }
}
