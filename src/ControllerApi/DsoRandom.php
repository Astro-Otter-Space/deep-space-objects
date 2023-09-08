<?php

namespace App\ControllerApi;

use App\Managers\DsoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DsoRandom extends AbstractFOSRestController
{

    public function __construct(private DsoManager $dsoManager) { }

    /**
     * @Route("/dso/random", name="api_get_dso_random", methods={"GET"})
     * @QueryParam(name="limit", requirements="\d+", default="5", description="Index end pagination")
     * @param ParamFetcherInterface $paramFetcher
     * @return View
     */
    public function getRandomDso(
        ParamFetcherInterface $paramFetcher
    ): View
    {
        $limit = (int)$paramFetcher->get('limit');
        try {
            $listDso = $this->dsoManager->randomDsoWithImages($limit);
        } catch (\Exception $e) {}

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $formatedData = $serializer->normalize($listDso->getIterator()->getArrayCopy());

        $view = View::create();
        $view->setData($formatedData);
        $view->setFormat('json');
        return $view;
    }
}
