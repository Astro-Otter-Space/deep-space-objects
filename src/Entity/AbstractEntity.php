<?php

namespace App\Entity;

use Elastica\Result;

/**
 * Class AbstractEntity
 * @package App\Entity
 */
abstract class AbstractEntity
{
    /**
     * Transform a Result item from ES into Entity
     *
     * @param $document
     * @return $this
     */
    public function buildObject($document)
    {
        $this->setElasticId($document->getId());
        foreach ($document->getData() as $field=>$data) {

            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', '', $field)));

            if (true == method_exists($this, $method)) {
                $this->$method($data);
            }
        }
        return $this;
    }

}
