<?php

namespace App\ControllerApi;

use App\Entity\Dso;
use App\Managers\DsoManager;
use App\Repository\DsoRepository;
use Elastica\Document;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ConfigurableViewHandlerInterface;
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

    const LIMIT = 20;

    private static $authorizedTypes = [
        'constellation' => 'const_id',
        'catalog' => 'catalog',
        'type' => 'type'
    ];

    /** @var DsoRepository  */
    private $dsoRepository;

    /**
     * DataController constructor.
     *
     * @param DsoRepository $dsoRepository
     */
    public function __construct(DsoRepository $dsoRepository)
    {
        $this->dsoRepository = $dsoRepository;
    }


    /**
     * @Rest\Get("/object/{id}", name="api_object_dso")
     *
     * @param string $id
     *
     * @return View
     * @throws \ReflectionException
     */
    public function getDso(string $id): View
    {
        /** @var Document $dso */
        $dso = $this->dsoRepository->getObjectById($id, false);

        if (is_null($dso)) {
            throw new NotFoundHttpException();
        }

        $view = View::create($dso->getData(), Response::HTTP_OK);
        $view->setFormat(self::JSON_FORMAT);

        return $view;
    }


    /**
     * @Rest\Get("/objects/{type}/{value}")
     * @return View
     */
    public function getDsoBy(string $type, string $value): View
    {

    }

}
