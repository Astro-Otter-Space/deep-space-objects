<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class Observation
 *
 * @package App\Controller
 */
class ObservationController extends AbstractController
{

    /**
     * @Route({
     *  "en": "/observations-list",
     *  "fr": "/liste-observations",
     *  "es": "/observations-list",
     *  "pt": "/observations-list",
     *  "de": "/observations-list"
     * }, name="observation_list")
     *
     * @return Response
     */
    public function list()
    {
        /** @var Response $response */
        $response = new Response();

        return $response;
    }

    /**
     * @param $name
     */
    public function show($name)
    {

    }

    /**
     *
     */
    public function add()
    {

    }

    /**
     *
     */
    public function delete()
    {

    }

}
