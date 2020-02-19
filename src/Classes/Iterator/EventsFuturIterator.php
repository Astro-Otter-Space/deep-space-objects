<?php


namespace App\Classes\Iterator;

use App\Entity\ES\Event;
use Iterator;

/**
 * Class EventsFuturIterator
 *
 * @package App\Classes\Iterator
 */
class EventsFuturIterator extends \FilterIterator
{
    /** @var \DateTimeInterface */
    private $dateFilter;

    /**
     * EventsFuturIterator constructor.
     *
     * @param Iterator $iterator
     *
     * @throws \Exception
     */
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator);
        $this->dateFilter = new \DateTime();
    }


    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Event $event */
        $event = $this->getInnerIterator()->current();
        $event->setEventDate($event->getEventDate(), false);

        return ($event->getEventDate()->getTimestamp() > $this->dateFilter->getTimestamp());
    }

}
