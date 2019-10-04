<?php

namespace App\Entity;

use Traversable;

/**
 * Class ListConstellation
 * @package App\Entity
 */
class ListConstellation implements \IteratorAggregate
{
    /** @var array|Traversable */
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

    // TODO add Yield insteed ArrayIterator : https://www.pmg.com/blog/generators-iterator-aggregate-php/?cn-reloaded=1
}
