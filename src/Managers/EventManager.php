<?php

namespace App\Managers;

use App\Entity\ES\Event;
use App\Repository\EventRepository;
use Elastica\Exception\ElasticsearchException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    /**
     * EventManager constructor.
     *
     * @param EventRepository $eventRepository
     * @param $locale
     */
    public function __construct(EventRepository $eventRepository, $locale)
    {
        $this->eventRepository = $eventRepository;
        $this->locale = $locale;
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
