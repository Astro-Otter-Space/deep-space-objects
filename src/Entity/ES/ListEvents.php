<?php

namespace App\Entity\ES;

use Exception;
use Traversable;

/**
 * Class ListEvents
 *
 * @package App\Entity\ES
 */
class ListEvents implements \IteratorAggregate
{
    protected $listEvents = [];

    /**
     * @param Event $event
     */
    public function addEvent(Event $event): void
    {
        $this->listEvents[] = $event;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->listEvents);
    }
}
