<?php

namespace App\Entity\ES;

use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
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
     * @param DTOInterface $dso
     */
    public function addDso(DTOInterface $dso): void
    {
        $this->listDso[] = $dso;
    }
}
