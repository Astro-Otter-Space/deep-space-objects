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
    protected $listObservations = [];

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
    public function addObservation(Observation $observation)
    {
        $this->listObservations[] = $observation;
    }

}
