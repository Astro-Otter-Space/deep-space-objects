<?php

namespace App\Controller;

use App\Entity\AbstractEntity;
use App\Entity\Observation;
use App\Forms\ObservationFormType;
use App\Managers\DsoManager;
use App\Managers\ObservationManager;
use App\Security\User;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class Observation
 *
 * @package App\Controller
 */
class ObservationController extends AbstractController
{
    /** @var ObservationManager  */
    private $observationManager;
    /** @var DsoManager  */
    private $dsoManager;

    /**
     * ObservationController constructor.
     *
     * @param ObservationManager $observationManager
     * @param DsoManager $dsoManager
     */
    public function __construct(ObservationManager $observationManager, DsoManager $dsoManager)
    {
        $this->observationManager = $observationManager;
        $this->dsoManager = $dsoManager;
    }

    /**
     * @Route({
     *  "en": "/observations-list",
     *  "fr": "/liste-des-observations",
     *  "es": "/observations-list",
     *  "pt": "/observations-list",
     *  "de": "/observations-list"
     * }, name="observation_list")
     *
     * @return Response
     */
    public function list()
    {
        $params['geojson'] = json_encode([]);

        /** @var Response $response */
        $response = $this->render('pages/observations.html.twig', $params);
        $response->setPublic();

        return $response;
    }


    /**
     * @Route({
     *  "en": "/_observations",
     *  "fr": "/_observations",
     *  "es": "/_observations",
     *  "pt": "/_observations",
     *  "de": "/_observations"
     * }, name="observation_list_ajax")
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function geosjonAjax()
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $this->observationManager->getAllObservation()
        ];

        return new JsonResponse($geojson, Response::HTTP_OK);
    }

    /**
     * @Route("/observation/{name}", name="observation_show")
     *
     * @param string $name
     *
     * @return Response
     * @throws ReflectionException
     */
    public function show($name): Response
    {
        $params = [];

        $id = md5($name);

        /** @var Observation $observation */
        $observation = $this->observationManager->buildObservation($id);

        $params["observation"] = $observation;
        $params['data'] = $this->observationManager->formatVueData($observation);
        $params['list_dso'] = $this->dsoManager->buildListDso($observation->getDsoList());

        $params['coordinates'] = [
            'lon' => $observation->getLocation()['coordinates'][0],
            'lat' => $observation->getLocation()['coordinates'][1]
        ];

        /** @var Response $response */
        $response = $this->render('pages/observation.html.twig', $params);
        $response->setPublic();

        return $response;
    }

    /**
     * Add observation page
     *
     * @Route({
     *  "en": "/add-observation",
     *  "fr": "/ajouter-observation",
     *  "es": "/add-observation",
     *  "pt": "/add-observation",
     *  "de": "/add-observation"
     * }, name="add_observation")
     * @param Request $request
     *
     * @return Response
     * @throws ExceptionInterface
     */
    public function add(Request $request)
    {
        $params = [];
        $isValid = false;

        /** @var Observation $observation */
        $observation = new Observation();
        $options = [
            'method' => 'POST',
            'action' => $this->get('router')->generate('add_observation'),
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ];

        $form = $this->createForm(ObservationFormType::class, $observation, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                /** @var Observation $observation */
                $observation = $form->getData();

                $isValid = $this->observationManager->addObservation($observation);
                if (true === $isValid) {
                    $this->addFlash('form.success','form.ok.addDoc');
                } else {
                    $this->addFlash('form.failed','form.error.addDoc');
                }
            } else {
                $this->addFlash('form.failed','form.error.message');
            }
        }

        $params['formAddObservation'] = $form->createView();
        $params['is_valid'] = $isValid;

        /** @var Response $response */
        $response = new Response();
        $response->setPrivate();

        return $this->render('pages/observation_add.html.twig', $params, $response);
    }

    /**
     *
     */
    public function delete()
    {

    }

}
