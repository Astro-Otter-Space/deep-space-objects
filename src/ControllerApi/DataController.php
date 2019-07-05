<?php

namespace App\ControllerApi;

use App\Entity\Dso;
use App\Managers\DsoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DsoController
 *
 * @package App\Api\Controller
 */
final class DataController extends AbstractFOSRestController
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
     * @param string $dsoId
     *
     * @return View
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     *
     * @Rest\Get("/object/{$dsoId}", name="api_object_dso")
     */
    public function getItem($dsoId)
    {
        /** @var Dso $dso */
        $dso = $this->dsoManager->buildDso($dsoId);
        if (!$dso instanceof Dso) {
            throw new NotFoundHttpException();
        }

        return View::create($dso, Response::HTTP_OK);
    }


    /**
     * @param string $constId
     * @Rest\Get("/constellation/{$constId}", requirements={"\w+"}, name="api_constellation")
     */
    public function getConstellation($constId)
    {
        dump('coucou');
    }

}
