<?php

namespace App\ControllerApi;

use App\Managers\ConstellationManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;

class ConstellationItem extends AbstractFOSRestController
{

    public function __construct(
        private ConstellationManager $constellationManager
    ) {}

    /**
     * @Route("/constellation/item/{id}, name="api_get_item_constellation", methods={"GET"})
     * @param string $id
     * @return View
     */
    public function getConstellationItemAction(string $id): View
    {
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
