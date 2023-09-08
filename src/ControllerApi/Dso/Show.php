<?php

namespace App\ControllerApi\Dso;

use App\Controller\ControllerTraits\DsoTrait;
use App\ControllerApi\DataController;
use App\Managers\DsoManager;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class Show extends AbstractFOSRestController
{
    use DsoTrait, SymfonyServicesTrait;

    /**
     * @Rest\Get("/dso/item/{id}", name="api_get_dso_item")
     *
     * @param string $id
     * @param DsoManager $dsoManager
     * @return Response
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function __invoke(string $id, DsoManager $dsoManager): Response
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
