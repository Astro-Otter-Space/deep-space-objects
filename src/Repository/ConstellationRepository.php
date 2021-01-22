<?php

namespace App\Repository;

use App\Entity\DTO\DTOInterface;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\ES\Constellation;
use App\Entity\ES\ListConstellation;
use Elastica\Document;
use Elastica\Query;
use Elastica\Result;
use Elastica\Search;

/**
 * Class ConstellationRepository
 * @package App\Repository
 */
final class ConstellationRepository extends AbstractRepository
{

    const INDEX_NAME = 'constellations';

    const SEARCH_SIZE = 15;

    const URL_MAP = '/build/images/const_maps/%s.gif';

    const URL_IMG = '/build/images/const_thumbs/%s.jpg';

    private static $listSearchFields = [
        'id',
        'id.raw',
        'data.gen',
        'data.alt.alt'
    ];

    /**
     * @param string $id
     * @param bool $hydrate
     *
     * @return DTOInterface|null
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function getObjectById(string $id, bool $hydrate): ?DTOInterface
    {
        $resultDocument = $this->findById(ucfirst($id));
        if (0 < $resultDocument->getTotalHits()) {

            if ($hydrate) {
                $document = $resultDocument->getDocuments()[0];
                return $this->buildEntityFromDocument($document);
            }
            return $resultDocument->getDocuments()[0]->getData();
        }

        return null;
    }


    /**
     * Build a list of all constellation (88)
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function getAllConstellation(): \Generator
    {
        /** @var ListConstellation $listConstellation */
        $this->client->getIndex(self::INDEX_NAME);

        /** @var Query $query */
        $query = new Query();
        $query->setSize(100);
        $query->addSort(['order' => ['order' => 'asc']]);

        /** @var Search $search */
        $search = new Search($this->client);
        $result = $search->addIndex(self::INDEX_NAME)->search($query);

        if (0 < $result->count()) {
            foreach ($result->getDocuments() as $document) {
                yield $this->buildEntityFromDocument($document);
            }
        }
    }

    /**
     * Search autocomplete
     *
     * @param $searchTerm
     *
     * @return array
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function getConstellationsBySearchTerms($searchTerm): array
    {
        $list = [];

        if ('en' !== $this->getLocale()) {
            self::$listSearchFields[] = sprintf('alt_%s', $this->getLocale());
            self::$listSearchFields[] = sprintf('alt_%s.keyword', $this->getLocale());
        }

        $result = $this->requestBySearchTerms($searchTerm, self::$listSearchFields);
        if (0 < $result->getTotalHits()) {
            $list = array_map(function(Result $doc) {
                return $this->buildEntityFromDocument($doc->getDocument());
            }, $result->getResults());
        }

        return $list;
    }

    /**
     * Build an entity from result
     *
     * @param Document $document
     *
     * @return DTOInterface
     * @throws \JsonException
     */
    private function buildEntityFromDocument(Document $document): DTOInterface
    {
        return $this->buildDTO($document);
    }

    /**
     * @return Constellation
     */
    public function getEntity(): string
    {
        return Constellation::class;
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        return self::INDEX_NAME;
    }

    /**
     *
     */
    protected function getDTO(): string
    {
        return ConstellationDTO::class;
    }
}
