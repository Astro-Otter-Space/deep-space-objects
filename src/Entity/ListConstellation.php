<?php

namespace App\Entity;

use Traversable;

/**
 * Class ListConstellation
 * @package App\Entity
 */
class ListConstellation implements \IteratorAggregate
{
    protected $listConstellation = [];

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->listConstellation);
    }

    /**
     * @param Constellation $constellation
     */
    public function addConstellation(Constellation $constellation)
    {
        $this->listConstellation[] = $constellation;
    }
}