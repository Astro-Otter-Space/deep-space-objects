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

class Search extends AbstractFOSRestController
{

    /**
     * @Route("/search", name="api_search_collection", methods={"GET"})
     * @QueryParam(name="term", requirements="\w+", default="")
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
        $searchTerm = strtolower(filter_var($paramFetcher->get('term'), FILTER_SANITIZE_STRING));
        $dsoItems = $constellationItems = [];

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        try {
            $listDso = $dsoManager->searchDsoByTerms($searchTerm);
        } catch (\Exception $e) {
            throw new WsException($e->getMessage());
        } finally {
            $arrayDso = $listDso->getIterator()->getArrayCopy();
            $listDso = $serializer->normalize($arrayDso, null, [AbstractNormalizer::GROUPS => 'search']);
        }


        $formatedData = [...$listDso, []];
        $view = View::create();
        $view->setData(array_filter($formatedData));
        $view->setFormat('json');
        return $view;
    }
}
