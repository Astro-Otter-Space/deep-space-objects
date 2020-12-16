<?php

namespace App\Entity\ES;

use Elastica\Document;

/**
 * Class AbstractEntity
 * @package App\Entity
 */
abstract class AbstractEntity
{



    /**
     * Same method as above but with ReflectionClass
     *
     * @TODO : try with Symfony Serializer, ObjectNormalizer
     * @param Document $document
     * @return $this
     * @throws \ReflectionException
     */
    public function buildObjectR(Document $document): self
    {
        $this->setElasticId($document->getId());

        if (array_key_exists('data', $document->getData())) {
            $dataDocument = array_merge($document->getData(), $document->getData()['data']);
            unset($dataDocument['data']);
        } else {
            $dataDocument = $document->getData();
        }

        // Transform snake_case keys into CamelCase keys
        $keys = array_map(static function ($i) {
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

            if (in_array($property->getName(), $this->getListFieldsNoMapping(), true)) {
                $property->setAccessible(true);
                $property->setValue($this, null);
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($this, $dataDocument[$property->getName()]);
        }

        // TODO verifier quand même ce truc...
        if (array_key_exists('alt', $dataDocument)) {
            $this->setAlt($dataDocument['alt']);
        }

        if (array_key_exists('description', $dataDocument)) {
            $this->setDescription($dataDocument['description']);
        }

        return $this;
    }

    abstract protected function getListFieldsNoMapping();
}
