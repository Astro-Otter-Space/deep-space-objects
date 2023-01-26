<?php

namespace App\Repository;

use App\Entity\DTO\DTOInterface;
use App\Helpers\UrlGenerateHelper;
use Elastica\Bulk;
use Elastica\Client;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use Elastica\Search;
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
    protected string $locale;

    protected Client $client;

    protected SerializerInterface $serializer;
    protected UrlGenerateHelper $urlGeneratorHelper;

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
    public function __construct(
        Client $client,
        SerializerInterface $serializer,
        UrlGenerateHelper $urlGeneratorHelper
    )
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->urlGeneratorHelper = $urlGeneratorHelper;
    }

    abstract protected function getEntity();
    abstract protected function getDTO();
    abstract protected function getIndex();

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale ?? 'en';
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
        $dto = $this->getDTO();

        $normalizer = [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];
        $encoder = [new JsonEncoder()];
        $serializer = new Serializer($normalizer, $encoder);

        /** @var $hydratedEntity */
        $hydratedEntity = $serializer->deserialize(json_encode($document->getData(), JSON_THROW_ON_ERROR), $entity, 'json');

        /** @var DTOInterface $dto */
        return new $dto($hydratedEntity, $this->getLocale(), $document->getId());
    }

    /**
     * Return document by Id
     *
     * @param $id
     * @return ResultSet
     */
    protected function findById($id): ResultSet
    {
        //$this->client->getIndex($this->getIndex());

        $matchQuery = new Query\MatchQuery();
        $matchQuery->setField('id', $id);

        $search = new Search($this->client);

        /** @var ResultSet $resultSet */
        return $search
            ->addIndex($this->getIndex())
            ->search($matchQuery);
    }

    /**
     * @param string $searchTerm
     * @param array $listSearchFields
     *
     * @return ResultSet
     */
    public function requestBySearchTerms(string $searchTerm, array $listSearchFields): ResultSet
    {
//        $this->client->getIndex($this->getIndex());

        $query = new Query\MultiMatch();
        $query->setFields($listSearchFields);
        $query->setQuery($searchTerm);
        $query->setType('phrase_prefix');

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
        $elasticType = $elasticIndex->getType('_doc');

        $response = $elasticType->addDocument($document);

        $elasticIndex->refresh();

        return $response;
    }

    /**
     * @param array $listItems
     *
     * @return bool
     */
    public function bulkImport(array $listItems): bool
    {
        $bulk = new Bulk($this->client);
        $bulk->setIndex($this->getIndex())->setType('_doc');

        foreach ($listItems as $doc) {
            /** @var Document $doc */
            $docEs = new Document($doc['idDoc'], $doc['data']);
            $action = null;
            if ('update' === $doc['mode']) {
                $action = new Bulk\Action\UpdateDocument($docEs);

            } else if ('create' === $doc['mode']) {
                $action = new Bulk\Action\CreateDocument($docEs);
            }
            if (!is_null($action)) {
                $bulk->addAction($action);
            }
        }

        $responseBulk = $bulk->send();

        // Refresh index
        $this->client->getIndex($this->getIndex())->refresh();

        return $responseBulk->isOk();
    }

}
