<?php

namespace App\ControllerApi;

use App\Controller\ControllerTraits\DsoTrait;
use App\Managers\DsoManager;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DsoItem extends AbstractFOSRestController
{
    use DsoTrait, SymfonyServicesTrait;

    /**
     * @Rest\Get("/dso/item/{id}", name="api_get_dso_item")
     *
     * @param string $id
     * @param DsoManager $dsoManager
     * @return View
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(string $id, DsoManager $dsoManager): View
    {
        $dso = $dsoManager->getDso($id);
        $codeHttp = Response::HTTP_OK;

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $formatedData = $serializer->normalize($dso);

        $view = $this->view($formatedData, $codeHttp);
        $view->setFormat(DataController::JSON_FORMAT);

        return $this->handleView($view);
    }
}
