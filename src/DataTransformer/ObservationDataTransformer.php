<?php


namespace App\DataTransformer;

use App\Classes\Utils;
use App\Entity\ES\Observation;

/**
 * Class ObservationDataTransformer
 *
 * @package App\DataTransformer
 */
final class ObservationDataTransformer extends AbstractDataTransformer
{
    /**
     * @param $dto
     *
     * @return mixed|void
     */
    public function longView($dto): array
    {
        $data = [
            'user' => $dto->getUsername(),
            'location' => $dto->getLocationLabel(),
            'instrument' => $dto->getInstrument(),
            'diameter' => $dto->getDiameter(),
            'focal' => $dto->getFocal(),
            'report' => Utils::numberFormatByLocale($dto->getFocal()/$dto->getDiameter()),
            'mount' => $dto->getMount(),
            'ocular' => implode(Utils::DATA_CONCAT_GLUE, $dto->getOcular())
        ];

        return array_filter($data, function($value) {
            return (false === empty($value));
        });
    }

}
