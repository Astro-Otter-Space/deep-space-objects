<?php


namespace App\DataTransformer;

use App\Entity\Dso;
use App\Entity\Observation;

/**
 * Class AbstractDataTransformer
 *
 * @package App\DataTransformer
 */
abstract class AbstractDataTransformer
{
    /**
     * @param Dso|Observation $entity
     * @return mixed
     */
    abstract protected function toArray($entity);
}
