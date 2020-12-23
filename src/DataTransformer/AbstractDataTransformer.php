<?php

namespace App\DataTransformer;

use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Dso;
use App\Entity\ES\Event;
use App\Entity\ES\Observation;

/**
 * Class AbstractDataTransformer
 *
 * @package App\DataTransformer
 */
abstract class AbstractDataTransformer
{
    /**
     * @param DTOInterface $dto
     *
     * @return mixed
     */
    abstract protected function toArray(DTOInterface $dto);
}
