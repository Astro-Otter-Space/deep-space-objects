<?php

namespace App\Managers;

use App\Entity\ES\Event;
use App\Helpers\UrlGenerateHelper;
use App\Repository\EventRepository;
use Elastica\Exception\ElasticsearchException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EventManager
 *
 * @package App\Managers
 */
class EventManager
{
    /** @var EventRepository  */
    private $eventRepository;

    /** @var string */
    private $locale;

    /** @var UrlGenerateHelper */
    private $urlGeneratorHelper;

    /** @var TranslatorInterface  */
    private $translator;

    /**
     * EventManager constructor.
     *
     * @param EventRepository $eventRepository
     * @param $locale
     * @param UrlGenerateHelper $urlGeneratorHelper
     * @param TranslatorInterface $translator
     */
    public function __construct(EventRepository $eventRepository, $locale, UrlGenerateHelper $urlGeneratorHelper, TranslatorInterface $translator)
    {
        $this->eventRepository = $eventRepository;
        $this->locale = $locale;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
        $this->translator= $translator;
    }

    /**
     * @param $id
     *
     * @return Event|null
     * @throws \ReflectionException
     */
    public function buildEvent($id)
    {
        /** @var Event|null $event */
        $event = $this->eventRepository->setLocale($this->locale)->getEventById($id);
        if (!is_null($event)) {
            $event->setFullUrl($this->urlGeneratorHelper->generateUrl($event, Router::ABSOLUTE_PATH, $this->locale));
        }

        return $event;
    }

    /**
     * @param $terms
     *
     * @return array
     */
    public function buildSearchEventByTerms($terms): array
    {
        /**
         * @return \Generator
         */
        $listResults = function() use ($terms) {
            yield from $this->eventRepository->setLocale($this->locale)->getEventBySearchTerms($terms);
        };

        return call_user_func("array_merge", array_map(function (Event $event) {
            $formatDate = $this->translator->trans('dateFormatLong');

            /** @var \DateTimeInterface $eventDate */
            $event->setEventDate($event->getEventDate(), false);

            return [
                'id' => $event->getId(),
                'ajaxValue' => $event->getName(),
                'label' => $event->getEventDate()->format($formatDate),
                'url' => $this->urlGeneratorHelper->generateUrl($event, Router::ABSOLUTE_PATH, $this->locale),
                'type' => EventRepository::INDEX_NAME
            ];
        }, iterator_to_array($listResults())));
    }

    /**
     * @return array
     */
    public function getAllEvents(): array
    {
        /** @return \Generator
         * @var  $listEvents
         */
        $listEvents = function() {
            yield from $this->eventRepository->setLocale($this->locale)->getAllFuturEvents();
        };

        return array_map(function(Event $event) {
            /** @var \IntlDateFormatter $formatter */
            $formatter = \IntlDateFormatter::create(
                $this->locale,
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::SHORT,
                null,
                \IntlDateFormatter::GREGORIAN,
                ''
            );

            return [
                'type' => 'Feature',
                'properties' => [
                    'name' => $event->getName(),
//                    'full_url' => ->generateUrl($observation),
                    'date' => $formatter->format($event->getEventDate()),
                ],
                'geometry' => $event->getLocation()
            ];
        }, iterator_to_array($listEvents()));
    }

    /**
     * Normalize Entity into JSON
     *
     * @param Event $event
     *
     * @return bool|string
     * @throws ExceptionInterface
     */
    public function addEvent(Event $event)
    {
        /** @var ObjectNormalizer $normalizer */
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());

        /** @var Serializer $serialize */
        $serialize = new Serializer([$normalizer]);

        $eventData = $serialize->normalize($event, null, ['attributes' => $event->getFieldsObjectToJson()]);

        try {
            $responseEs = $this->eventRepository->addEvent($event->getId(), $eventData);
            if (Response::HTTP_CREATED === $responseEs->getStatus()) {
                return true;
            } else {
                return $responseEs->getErrorMessage();
            }
        } catch(ElasticsearchException $e) {
            return $e->getMessage();
        }
    }
}
