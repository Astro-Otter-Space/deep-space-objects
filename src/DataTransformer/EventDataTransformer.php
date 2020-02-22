<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\ES\Event;

/**
 * Class EventDataTransformer
 *
 * @package App\DataTransformer
 */
final class EventDataTransformer extends AbstractDataTransformer
{

    /**
     * @inheritDoc
     * @param Event $entity
     */
    public function toArray($entity): array
    {

        $data = [
            'event.eventDate.label' => $entity->getEventDate()->format('Y-m-d H:i:s'),
            'event.locationLabel.label' => $entity->getLocationLabel(),
            'event.tarif.label' => $entity->getTarif() ?? 0,
            'event.public.label' => Utils::listEventPublic()[$entity->getPublic()],
            'event.numberEntrant.label' => $entity->getNumberEntrant() ?? null,
            'event.organiserName.label' => $entity->getOrganiserName(),
            'event.organiserTel.label' => $entity->getOrganiserTel(),
            'event.organiserMail.label' => $entity->getOrganiserMail()
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }
}
