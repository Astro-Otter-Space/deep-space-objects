<?php

namespace App\Entity\ES;

use Traversable;

/**
 * Class ListObservations
 *
 * @package App\Entity
 */
class ListObservations implements \IteratorAggregate
{
    protected array $listObservations = [];

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->listObservations);
    }

    /**
     * @param Observation $observation
     */
    public function addObservation(Observation $observation): void
    {
        $this->listObservations[] = $observation;
    }


}
