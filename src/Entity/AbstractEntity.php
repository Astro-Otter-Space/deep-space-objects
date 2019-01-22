<?php

namespace App\Entity;

use Elastica\Document;

/**
 * Class AbstractEntity
 * @package App\Entity
 */
abstract class AbstractEntity
{
    const DATA_GLUE = '.';

    const DATA_CONCAT_GLUE = ' - ';

    const UNASSIGNED = 'unassigned';

    /**
     * Transform a Result item from ES into Entity
     *
     * @param $document
     * @return $this
     * @throws \ReflectionException
     */
    public function buildObject(Document $document)
    {


        /*foreach ($document->getData() as $field=>$data) {

            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', '', $field)));
            if (is_array($data) && "geometry" != $field) {
                $object = $this;
                array_walk($data, function($value, $field) use (&$object) {
                    $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
                    if (true === method_exists($this, $method)) {
                        $object->$method($value);
                    }
                });
            }

            if (true == method_exists($this, $method)) {
                $this->$method($data);
            }

        }*/
        $listNotFields = ['locale', 'geometry', 'image', 'fullUrl', 'elasticId', 'order', 'data'];

        $dataDocument = (array)$document->getData();
        $dataDocument = array_merge($dataDocument, $dataDocument['data']);
        unset($dataDocument['data']);

        array_walk_recursive($dataDocument, function($value, &$key) {
            $key = str_replace(' ', '', ucwords(str_replace('_', '', $key)));
        });
        dump($dataDocument);

        /** @var \ReflectionClass $reflector */
        $reflector = new \ReflectionClass($this);
        foreach ($reflector->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $dataDocument)) {
                continue;
            }

            if (in_array($property->getName(), $listNotFields)) {
                $property->setAccessible(true);
                $property->setValue($this, null);
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($this, $dataDocument[$property->getName()]);
        }
        $this->setElasticId($document->getId());
        return $this;
    }

}
