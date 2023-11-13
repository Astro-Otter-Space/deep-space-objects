<?php

namespace App\ControllerApi;

use App\Managers\ConstellationManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConstellationCollection extends AbstractFOSRestController
{

    public function __construct(private ConstellationManager $constellationManager) {}

    /**
     * @Route("/constellation/list", name="api_get_constellation_collection", methods={"GET"})
     * @return View
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function getConstellationsCollection(): View
    {
        try {
            $listConstellations = $this->constellationManager->getAllConstellations(null);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Constellations list not found');
        }

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $formatedData = $serializer->normalize($listConstellations->getIterator()->getArrayCopy());

        $view = View::create();
        $view->setData($formatedData);
        $view->setFormat('json');
        return $view;
    }
}
