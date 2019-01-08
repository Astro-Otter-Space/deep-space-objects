<?php

namespace App\Entity;

use Traversable;

/**
 * Class ListDso
 * @package App\Entity
 */
class ListDso implements \IteratorAggregate
{

    protected $listDso = [];

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->listDso);
    }

    /**
     * @param Dso $dso
     */
    public function addDso(Dso $dso)
    {
        $this->listDso[] = $dso;
    }
}
