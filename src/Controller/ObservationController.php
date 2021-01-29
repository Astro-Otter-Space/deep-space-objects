<?php

namespace App\Controller;

use App\Controller\ControllerTraits\DsoTrait;
use App\Entity\ES\Event;
use App\Entity\ES\Observation;
use App\Forms\ObservationFormType;
use App\Forms\ObservingEventFormType;
use App\Helpers\UrlGenerateHelper;
use App\Managers\DsoManager;
use App\Managers\EventManager;
use App\Managers\ObservationManager;
use Elastica\Exception\NotFoundException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    use DsoTrait;

    private ObservationManager $observationManager;
    private DsoManager $dsoManager;
    private EventManager $eventManager;
    private UrlGenerateHelper $urlGeneratorHelper;

    /**
     * ObservationController constructor.
     *
     * @param ObservationManager $observationManager
     * @param DsoManager $dsoManager
     * @param TranslatorInterface $translator
     * @param EventManager $eventManager
     * @param UrlGenerateHelper $urlGeneratorHelper
     */
    public function __construct(ObservationManager $observationManager, DsoManager $dsoManager, TranslatorInterface $translator, EventManager $eventManager, UrlGenerateHelper $urlGeneratorHelper)
    {
        $this->observationManager = $observationManager;
        $this->dsoManager = $dsoManager;
        $this->eventManager = $eventManager;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
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
    public function geojonObservationsAjax(): JsonResponse
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $this->observationManager->getAllObservation()
        ];


        /** @var JsonResponse $response */
        $response = new JsonResponse($geojson, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }


    /**
     * @Route({
     *  "en": "/_events",
     *  "fr": "/_events",
     *  "es": "/_events",
     *  "pt": "/_events",
     *  "de": "/_events"
     * }, name="events_list_ajax")
     * @return JsonResponse
     *
     */
    public function geojsonEventsAjax(): JsonResponse
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $this->eventManager->getAllEvents()
        ];

        /** @var JsonResponse $response */
        $response = new JsonResponse($geojson, Response::HTTP_OK);
        $response->setPublic()->setSharedMaxAge(0);

        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
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

        $params['breadcrumbs'] = $this->buildBreadcrumbs($observation, $this->get('router'), $observation->getName());

        /** @var Response $response */
        $response = $this->render('pages/observation.html.twig', $params);
        $response->setPublic();
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);

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
                    $messageOk = $this->translator->trans('form.ok.addDoc', ['%url%' => $this->urlGeneratorHelper->generateUrl($observation)]);
                    $this->addFlash('form.success', $messageOk);
                } else {
                    $this->addFlash('form.failed', $this->translator->trans('form.error.addDoc'));
                }
            } else {
                $this->addFlash('form.failed', $this->translator->trans('form.error.message'));
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

                $isValid = $this->eventManager->addEvent($event);
                if (true === $isValid) {
                    $messageOk = $this->translator->trans('form.ok.addDoc', ['%url%' => $this->urlGeneratorHelper->generateUrl($event)]);
                    $this->addFlash('form.success', $messageOk);
                } else {
                    $this->addFlash('form.failed', $this->translator->trans('form.error.addDoc'));
                }
            } else {
                $this->addFlash('form.failed', $this->translator->trans('form.error.message'));
            }
        }

        $params['formAddEvent'] = $form->createView();
        $params['is_valid'] = $isValid;

        /** @var Response $response */
        $response = new Response();
        $response->setPrivate();

        return $this->render('pages/event_add.html.twig', $params, $response);
    }

    /**
     * @param Request $request
     * @param $name
     * @Route({
     *  "en": "/event/{name}",
     *  "fr": "/evenement/{name}",
     *  "es": "/event/{name}",
     *  "pt": "/event/{name}",
     *  "de": "/event/{name}"
     * }, name="event_show")
     *
     * @return Response
     * @throws ReflectionException
     */
    public function showEvent(Request $request, string $name): Response
    {
        $params = [];
        $id = md5($name);

        /** @var Event $event */
        $event = $this->eventManager->buildEvent($id);
        if (is_null($event)) {
            throw new NotFoundHttpException();
        }

        $params["event"] = $event;
        $params['data'] = $this->eventManager->formatVueData($event);
        $params['coordinates'] = [
            'lon' => $event->getLocation()['coordinates'][0],
            'lat' => $event->getLocation()['coordinates'][1]
        ];
        $params['breadcrumbs'] = $this->buildBreadcrumbs($event, $this->get('router'), $event->getName());

        /** @var Response $response */
        $response = new Response();
        $response->setSharedMaxAge(LayoutController::HTTP_TTL);
        $response->setPublic();

        /** @var Response $response */
        return $this->render('pages/event.html.twig', $params, $response);
    }
}
