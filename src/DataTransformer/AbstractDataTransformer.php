<?php

namespace App\DataTransformer;

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
     * @param Dso|Observation|Event $entity
     * @return mixed
     */
    abstract protected function toArray($entity);
}
