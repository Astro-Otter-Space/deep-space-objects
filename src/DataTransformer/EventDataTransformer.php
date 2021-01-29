<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\DTO\DTOInterface;
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
     *
     * @param Event $dto
     *
     * @return array
     */
    public function longView(DTOInterface $dto): array
    {

        $data = [
            'event.eventDate.label' => $dto->getEventDate()->format('Y-m-d H:i:s'),
            'event.locationLabel.label' => $dto->getLocationLabel(),
            'event.tarif.label' => $dto->getTarif() ?? 0,
            'event.public.label' => Utils::listEventPublic()[$dto->getPublic()],
            'event.numberEntrant.label' => $dto->getNumberEntrant() ?? null,
            'event.organiserName.label' => $dto->getOrganiserName(),
            'event.organiserTel.label' => $dto->getOrganiserTel(),
            'event.organiserMail.label' => $dto->getOrganiserMail()
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }
}
