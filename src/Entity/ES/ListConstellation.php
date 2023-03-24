<?php

namespace App\Entity\ES;

use App\Entity\DTO\DTOInterface;
use Traversable;

/**
 * Class ListConstellation
 * @package App\Entity
 */
class ListConstellation implements \IteratorAggregate
{
    /** @var array|Traversable */
    protected array|Traversable $listConstellation = [];

    /**
     * @return \ArrayIterator|Traversable
     */
    public function getIterator(): Traversable|\ArrayIterator
    {
        return new \ArrayIterator($this->listConstellation);
    }

    /**
     * @param DTOInterface $constellation
     */
    public function addConstellation(DTOInterface $constellation): void
    {
        $this->listConstellation[] = $constellation;
    }

    // TODO add Yield insteed ArrayIterator : https://www.pmg.com/blog/generators-iterator-aggregate-php/?cn-reloaded=1
}
