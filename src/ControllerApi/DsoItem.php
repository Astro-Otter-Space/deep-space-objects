<?php

namespace App\ControllerApi;

use App\Controller\ControllerTraits\DsoTrait;
use App\Managers\DsoManager;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 *
 */
class DsoItem extends AbstractFOSRestController
{
    use DsoTrait, SymfonyServicesTrait;

    public function __construct(
        private DsoManager $dsoManager
    ) { }

    /**
     * @Route("/dso/item/{id}", name="api_get_dso_item", methods={"GET"})
     *
     * @param string $id
     * @return View
     *
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function getDsoItemAction(string $id): View
    {
        try {
            $dso = $this->dsoManager->getDso($id);
        } catch (\Exception $e) {
            throw new NotFoundHttpException(sprintf('Document "%s" not find.', $id));
        }

        $codeHttp = Response::HTTP_OK;

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $formatedData = $serializer->normalize($dso);

        $view = View::create();
        $view->setData($formatedData);

        return $view;
    }
}
