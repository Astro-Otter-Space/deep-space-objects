<?php

namespace App\Controller;

use App\Entity\Constellation;
use App\Entity\Dso;
use App\Helpers\UrlGenerateHelper;
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
     * @param ConstellationRepository $constellationRepository
     * @param DsoRepository $dsoRepository
     * @param DsoManager $dsoManager
     * @return Response
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function show(string $id, ConstellationRepository $constellationRepository, DsoRepository $dsoRepository, DsoManager $dsoManager, UrlGenerateHelper $urlGeneratorHelper): Response
    {
        $result = [];

        /** @var Router $router */
        $router = $this->get('router');

        /** @var Serializer $serializer */
        $serializer = $this->container->get('serializer');

        /** @var Constellation $constellation */
        $constellation = $constellationRepository->getObjectById($id);

        // Retrieve list of Dso from the constellation
        $listDso = $dsoRepository->getObjectsByConstId($constellation->getId(), null,20);
        /** @var Dso $dso */
        foreach ($listDso->getIterator() as $dso) {
            $dso->setImage($dsoManager->getAstrobinImage($dso->getAstrobinId(), $dso->getId(), 'url_regular'));
            $dso->setFullUrl($dsoManager->getDsoUrl($dso));
        }

        $constellation->setListDso($listDso);
        $constellation->setFullUrl($urlGeneratorHelper->generateUrl($constellation));

        $result['constellation'] = $serializer->serialize($constellation, 'json');

        // TODO : refactor this with DsoManager::buildListDso()
        $result['list_dso'] = array_map(function(Dso $dsoChild) use ($dsoManager) {
            return array_merge($dsoManager->buildSearchData($dsoChild), ['image' => $dsoChild->getImage()]);
        }, iterator_to_array($constellation->getListDso()));

        $result['link_download'] = $router->generate('download_map', ['id' => $constellation->getId()]);

        /** @var Response $response */
        $response = $this->render('pages/constellation.html.twig', $result);
        $response->headers->set('X-Constellation-Id', $constellation->getElasticId());
        $response->setPublic();

        return $response;
    }


    /**
     * @Route("/constellations", name="constellation_list")
     * @return Response
     */
    public function list()
    {
        return new Response("coucou");
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
