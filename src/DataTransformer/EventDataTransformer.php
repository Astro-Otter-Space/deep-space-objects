<?php


namespace App\DataTransformer;

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
            'location' => $entity->getLocationLabel(),
            'tarif' => $entity->getTarif() ?? 0,
            'public' => $entity->getPublic(),
            'numberEntrant' => $entity->getNumberEntrant() ?? null,
            'organiserName' => $entity->getOrganiserName(),
            'organiserTel' => $entity->getOrganiserTel(),
            'organiserMail' => $entity->getOrganiserMail()
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }
}
