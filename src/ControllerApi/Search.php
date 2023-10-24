<?php

namespace App\ControllerApi;

use App\Managers\ConstellationManager;
use App\Managers\DsoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class Search extends AbstractFOSRestController
{

    /**
     * @Route("/search", name="api_search_collection", methods={"GET"})
     * @QueryParam(name="term", requirements="[a-zA-Z0-9-_.%\s]+", default="")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param DsoManager $dsoManager
     * @param ConstellationManager $constellationManager
     * @return View
     */
    public function __invoke(
        ParamFetcherInterface $paramFetcher,
        DsoManager $dsoManager,
        ConstellationManager $constellationManager
    ): View
    {
        $searchTerm = strtolower(htmlspecialchars($paramFetcher->get('term')));

        dump($paramFetcher->get('term'), $searchTerm);
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        try {
            $listDso = $dsoManager->searchDsoByTerms($searchTerm);
        } catch (\Exception $e) {
            throw new WsException($e->getMessage());
        } finally {
            $listDso = $serializer->normalize($listDso, null, ['groups' => 'search']);
        }

        try {
            $listConstellation = $constellationManager->searchConstellationsByTerms($searchTerm);
        } catch (\Exception $e) {
            throw new WsException($e->getMessage());
        } finally {
            $listConstellation = $serializer->normalize($listConstellation, null, ['groups' => 'search']);
        }


        $formatedData = [...$listDso, []];
        $view = View::create();
        $view->setData(array_filter($formatedData));
        $view->setFormat('json');
        return $view;
    }
}
