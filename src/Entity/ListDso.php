<?php

namespace App\Entity;

use Traversable;

/**
 * Class ListDso
 * @package App\Entity
 */
class ListDso implements \IteratorAggregate
{

    private $listDso = [];

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->listDso);
    }
}
