<?php

namespace App\Api\Controller;

use App\Entity\Dso;
use App\Managers\DsoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DsoController
 *
 * @package App\Api\Controller
 * @Route("/api", name="api_")
 */
final class CatalogController extends AbstractFOSRestController
{
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
     * @param string $id
     *
     * @return View
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     *
     * @Rest\Get("/item/{$id}")
     */
    public function getItem(string $id)
    {
        /** @var Dso $dso */
        $dso = $this->dsoManager->buildDso($id);
        if (!$dso instanceof Dso) {
            throw new NotFoundHttpException();
        }

        return View::create($id, Response::HTTP_OK);
    }

}
