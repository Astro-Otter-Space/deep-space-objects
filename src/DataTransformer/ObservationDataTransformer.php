<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\Observation;

/**
 * Class ObservationDataTransformer
 *
 * @package App\DataTransformer
 */
final class ObservationDataTransformer extends AbstractDataTransformer
{
    /**
     * @param $entity
     *
     * @return mixed|void
     */
    public function toArray($entity)
    {
        $data = [
            'user' => $entity->getUsername(),
            'location' => $entity->getLocationLabel(),
            'instrument' => $entity->getInstrument(),
            'diameter' => $entity->getDiameter(),
            'focal' => $entity->getFocal(),
            'report' => Utils::numberFormatByLocale($entity->getFocal()/$entity->getDiameter()),
            'mount' => $entity->getMount(),
            'ocular' => implode(Observation::DATA_CONCAT_GLUE, $entity->getOcular())
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }

}
