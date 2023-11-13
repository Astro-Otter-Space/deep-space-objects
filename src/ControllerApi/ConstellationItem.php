<?php

namespace App\ControllerApi;

use App\Managers\ConstellationManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ConstellationItem extends AbstractFOSRestController
{

    public function __construct(
        private ConstellationManager $constellationManager
    ) {}

    /**
     * @Route("/constellation/item/{id}", name="api_get_item_constellation", methods={"GET"})
     * @param string $id
     * @return View
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function getConstellationItemAction(string $id): View
    {
        if (!in_array($id, $this->constellationManager->getAllConstellations(true), true)) {
            throw new NotFoundHttpException(sprintf('Constellation "%s" not find.', $id));
        }

        try {
            $constellation = $this->constellationManager->getConstellationById($id);
        } catch (\Exception $e) {
            throw new NotFoundHttpException(sprintf('Constellation "%s" not find.', $id));
        }
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $formatedData = $serializer->normalize($constellation);

        $view = View::create();
        $view->setData($formatedData);
        $view->setFormat('json');
        return $view;
    }

}
