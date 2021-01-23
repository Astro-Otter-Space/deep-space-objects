<?php

namespace App\Repository;

use App\Entity\DTO\DTOInterface;
use App\Entity\DTO\DsoDTO;
use App\Entity\ES\Constellation;
use App\Entity\ES\Dso;
use App\Entity\ES\Observation;
use App\Helpers\UrlGenerateHelper;
use Elastica\Bulk;
use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use Elastica\Search;
use Elastica\Type;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class AbstractRepository
 * @package App\Repository
 */
abstract class AbstractRepository
{
    protected $locale;

    /** @var Search  */
    protected $client;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var UrlGenerateHelper */
    protected $urlGeneratorHelper;

    public const FROM = 0;
    public const SMALL_SIZE = 10;
    public const SIZE = 20;
    public const MAX_SIZE = 9999;
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    /**
     * AbstractRepository constructor.
     *
     * @param Client $client
     * @param SerializerInterface $serializer
     * @param UrlGenerateHelper $urlGeneratorHelper
     */
    public function __construct(Client $client, SerializerInterface $serializer, UrlGenerateHelper $urlGeneratorHelper)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
    }

    /**
     * @return mixed
     */
    public function getLocale(): string
    {
        if (is_null($this->locale)) {
            $this->locale = 'en';
        }
        return $this->locale;
    }

    /**
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale): AbstractRepository
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Build DTO from Entity from ElasticSearch Document
     *
     * @param Document $document
     *
     * @return DTOInterface
     * @throws \JsonException
     */
    public function buildDTO(Document $document): DTOInterface
    {
        $entity = $this->getEntity();

        $normalizer = [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];
        $encoder = [new JsonEncoder()];
        $serializer = new Serializer($normalizer, $encoder);

        /** @var $object */
        $object = $serializer->deserialize(json_encode($document->getData(), JSON_THROW_ON_ERROR), $entity, 'json');
        $dto = $this->getDTO();

        /** @var DTOInterface $dto */
        $dto = new $dto($object, $this->getLocale(), $document->getId());
        $dto->setFullUrl($this->urlGeneratorHelper->generateUrl($dto));

        return $dto;
    }

    /**
     * Return document by Id
     *
     * @param $id
     * @return ResultSet
     */
    protected function findById($id): ResultSet
    {
        /** @var Constellation|Observation|Dso $entity */
        $entityName = $this->getEntity();
        $entity = new $entityName;

        $this->client->getIndex($this->getIndex());

        /** @var Query\Term $term */
        $matchQuery = new Query\Match();
        $matchQuery->setField('id', $id);

        /** @var Search $search */
        $search = new Search($this->client);

        /** @var ResultSet $resultSet */
        return $search->addIndex($this->getIndex())->search($matchQuery);
    }

    /**
     * @param $searchTerm
     * @param $listSearchFields
     * @return ResultSet
     */
    public function requestBySearchTerms($searchTerm, $listSearchFields): ResultSet
    {
        $this->client->getIndex($this->getIndex());

        /** @var Query\MultiMatch $query */
        $query = new Query\MultiMatch();
        $query->setFields($listSearchFields);
        $query->setQuery($searchTerm);
        $query->setType('phrase_prefix');

        /** @var Search $search */
        $search = new Search($this->client);
        $search->addIndex($this->getIndex());

        return $search->search($query);
    }

    /**
     * @param Document $document
     *
     * @return Response
     */
    public function addNewDocument(Document $document): Response
    {
        /** @var Index $elasticIndex */
        $elasticIndex = $this->client->getIndex($this->getIndex());
        /** @var Type $elasticType */
        $elasticType = $elasticIndex->getType('_doc');

        /** @var Response $response */
        $response = $elasticType->addDocument($document);

        $elasticIndex->refresh();

        return $response;
    }

    /**
     * @param $listItems
     *
     * @return bool
     */
    public function bulkImport($listItems): bool
    {
        /** @var Bulk $bulk */
        $bulk = new Bulk($this->client);
        $bulk->setIndex($this->getIndex())->setType('_doc');

        foreach ($listItems as $doc) {
            /** @var Document $doc */
            $docEs = new Document($doc['idDoc'], $doc['data']);

            if ('update' === $doc['mode']) {
                $action = new Bulk\Action\UpdateDocument($docEs);

            } else if ('create' === $doc['mode']) {
                $action = new Bulk\Action\CreateDocument($docEs);
            }
            $bulk->addAction($action);
        }

        /** @var Bulk\ResponseSet $responseBulk */
        $responseBulk = $bulk->send();

        // Refresh index
        $this->client->getIndex($this->getIndex())->refresh();

        return $responseBulk->isOk();
    }

    abstract protected function getEntity();
    abstract protected function getDTO();
    abstract protected function getIndex();
}
