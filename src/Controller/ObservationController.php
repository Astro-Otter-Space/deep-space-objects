<?php

namespace App\Controller;

use App\Entity\ES\Event;
use App\Entity\ES\Observation;
use App\Forms\ObservationFormType;
use App\Forms\ObservingEventFormType;
use App\Managers\DsoManager;
use App\Managers\EventManager;
use App\Managers\ObservationManager;
use Elastica\Exception\NotFoundException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    /** @var TranslatorInterface */
    private $translatorInterface;
    /** @var EventManager  */
    private $eventManager;

    /**
     * ObservationController constructor.
     *
     * @param ObservationManager $observationManager
     * @param DsoManager $dsoManager
     * @param TranslatorInterface $translatorInterface
     * @param EventManager $eventManager
     */
    public function __construct(ObservationManager $observationManager, DsoManager $dsoManager, TranslatorInterface $translatorInterface, EventManager $eventManager)
    {
        $this->observationManager = $observationManager;
        $this->dsoManager = $dsoManager;
        $this->translatorInterface = $translatorInterface;
        $this->eventManager = $eventManager;
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
    public function geosjonAjax(): JsonResponse
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
        if (is_null($observation)) {
            throw new NotFoundException();
        }

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
                    $messageOk = $this->translatorInterface->trans('form.ok.addDoc', ['%url%' => $observation->getFullUrl()]);
                    $this->addFlash('form.success', $messageOk);
                } else {
                    $this->addFlash('form.failed', $this->translatorInterface->trans('form.error.addDoc'));
                }
            } else {
                $this->addFlash('form.failed', $this->translatorInterface->trans('form.error.message'));
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

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route({
     *  "en": "/schedule-observing-event",
     *  "fr": "/organiser-une-soiree-observation",
     *  "es": "/schedule-observing-event",
     *  "pt": "/schedule-observing-event",
     *  "de": "/schedule-observing-event"
     * }, name="schedule_obs")
     *
     * @throws ExceptionInterface
     */
    public function scheduleObservation(Request $request): Response
    {
        $params = [];
        $isValid = false;

        $options = [
            'method' => 'POST',
            'action' => $this->generateUrl('schedule_obs'),
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ];

        /** @var Event $event */
        $event = new Event();

        $form = $this->createForm(ObservingEventFormType::class, $event, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var Event $event */
                $event = $form->getData();
                dump($event);
                die();
                $isValid = $this->eventManager->addEvent($event);
                if (true === $isValid) {
                    $messageOk = $this->translatorInterface->trans('form.ok.addDoc', ['%url%' => $event->getFullUrl()]);
                    $this->addFlash('form.success', $messageOk);
                } else {
                    $this->addFlash('form.failed', $this->translatorInterface->trans('form.error.addDoc'));
                }
            } else {
                $this->addFlash('form.failed', $this->translatorInterface->trans('form.error.message'));
            }
        }

        $params['formAddEvent'] = $form->createView();
        $params['is_valid'] = $isValid;

        /** @var Response $response */
        $response = new Response();
        $response->setPrivate();

        return $this->render('pages/schedule_observation.html.twig', $params, $response);
    }
}
