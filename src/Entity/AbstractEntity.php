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

    const URL_CONCAT_GLUE = '--';

    const COMA_GLUE = ',';

    const UNASSIGNED = 'unassigned';

    /**
     * Transform a Result item from ES into Entity
     * @deprecated
     * @param Document $document
     * @return $this
     */
    public function buildObject(Document $document)
    {
        $this->setElasticId($document->getId());

        foreach ($document->getData() as $field=>$data) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', '', $field)));
            if (is_array($data) && "geometry" != $field) {
                $object = $this;
                array_walk($data, function($value, $field) use (&$object) {
                    $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
                    if (true === method_exists($this, $method)) {
                        $object->$method($value);
                    }
                });
            } /*elseif ("geometry" === $field) {
                $object->setGeometry()
            }*/
            if (true == method_exists($this, $method)) {
                $this->$method($data, true);
            }
        }
        return $this;
    }


    /**
     * Same method as above but with ReflectionClass
     *
     * @TODO : try with Symfony Serializer, ObjectNormalizer
     * @param Document $document
     * @return $this
     * @throws \ReflectionException
     */
    public function buildObjectR(Document $document)
    {
        $this->setElasticId($document->getId());

        if (array_key_exists('data', $document->getData())) {
            $dataDocument = array_merge($document->getData(), $document->getData()['data']);
            unset($dataDocument['data']);
        } else {
            $dataDocument = $document->getData();
        }

        // Transform snake_case keys into CamelCase keys
        $keys = array_map(function ($i) {
            $parts = explode('_', $i);
            return array_shift($parts). implode('', array_map('ucfirst', $parts));
        }, array_keys($dataDocument));

        $dataDocument = array_combine($keys, $dataDocument);

        /** @var \ReflectionClass $reflector */
        $reflector = new \ReflectionClass($this);
        foreach ($reflector->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $dataDocument)) {
                continue;
            }

            if (in_array($property->getName(), $this->getListFieldsNoMapping())) {
                $property->setAccessible(true);
                $property->setValue($this, null);
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($this, $dataDocument[$property->getName()]);
        }

        if (array_key_exists('alt', $dataDocument)) {
            // TODO verifier quand même ce truc...
            $this->setAlt($dataDocument['alt']);
        }
        return $this;
    }

    abstract protected function getListFieldsNoMapping();
}
