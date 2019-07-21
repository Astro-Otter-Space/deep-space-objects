<?php

namespace App\ControllerApi;

use App\Entity\Dso;
use App\Managers\DsoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Serializer;

/**
 * Class DsoController
 *
 * @package App\Api\Controller
 */
final class DataController extends AbstractFOSRestController
{
    const JSON_FORMAT = 'json';

    /** @var DsoManager  */
    private $dsoManager;

    /**
     * DsoController constructor.
     *
     * @param DsoManager $dsoManager
     */
    public function __construct(DsoManager $dsoManager)
    {
        $this->dsoManager = $dsoManager;
    }


    /**
     * @Rest\View()
     * @Rest\Get("/object/{id}", name="api_object_dso")
     *
     * @param string $id
     *
     * @return View
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    public function getDso(string $id): View
    {
        /** @var Dso $dso */
        $dso = $this->dsoManager->buildDso($id);

        if (!$dso instanceof Dso) {
            throw new NotFoundHttpException();
        }

//        dump($dso);
        /** @var Serializer $serializer */
        $serializer = $this->container->get('serializer');

        $jsonDso = $serializer->serialize($dso, self::JSON_FORMAT);

//        dump($jsonDso);
        $view = View::create($dso, Response::HTTP_OK);
        $view->setFormat(self::JSON_FORMAT);

        return $view;
    }

}
