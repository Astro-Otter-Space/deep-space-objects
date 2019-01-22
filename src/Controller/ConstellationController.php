<?php

namespace App\Controller;

use App\Entity\Constellation;
use App\Entity\Dso;
use App\Managers\DsoManager;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ConstellationController
 * @package App\Controller
 */
class ConstellationController extends AbstractController
{

    /**
     * @Route("/constellation/{id}", name="constellation_show")
     * @param string $id
     * @param ConstellationRepository $constellationRepository
     * @param DsoRepository $dsoRepository
     * @param DsoManager $dsoManager
     * @return Response
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function show(string $id, ConstellationRepository $constellationRepository, DsoRepository $dsoRepository, DsoManager $dsoManager): Response
    {
//        https://vuejsexamples.com/a-multi-item-card-carousel-in-vue/
        $result = [];

        /** @var Constellation $constellation */
        $constellation = $constellationRepository->getObjectById($id);

        // Retrieve list of Dso from the constellation
        $listDso = $dsoRepository->getObjectsByConstId($constellation->getId(), 10);
        /** @var Dso $dso */
        foreach ($listDso->getIterator() as $dso) {
            $dso->setImage($dsoManager->getAstrobinImage($dso));
            $dso->setFullUrl($dsoManager->getDsoUrl($dso));
        }
        $constellation->setListDso($listDso);


        $result['constellation'] = $constellation;

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

}
