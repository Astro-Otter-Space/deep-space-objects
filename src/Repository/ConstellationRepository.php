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

    public const INDEX_NAME = 'constellations';

    public const URL_MAP = '/build/images/const_maps/%s.gif';

    public const URL_IMG = '/build/images/const_thumbs/%s.jpg';

    private static array $listSearchFields = [
        'id',
        'gen',
        'alt.alt'
    ];

    /**
     * @param string $id
     *
     * @return DTOInterface|null
     * @throws \JsonException
     */
    public function getObjectById(string $id): ?DTOInterface
    {
        $resultDocument = $this->findById(ucfirst($id));
        if (0 < $resultDocument->getTotalHits()) {
            $document = $resultDocument->getDocuments()[0];
            return $this->buildEntityFromDocument($document);
        }

        return null;
    }


    /**
     * Build a list of all constellation (88)
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function getAllConstellation(): array
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

        return array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $result->getResults());
    }

    /**
     * Search autocomplete
     *
     * @param $searchTerm
     *
     * @return array
     * @throws \JsonException
     */
    public function getConstellationsBySearchTerms($searchTerm): array
    {
        if ('en' !== $this->getLocale()) {
            self::$listSearchFields[] = sprintf('alt_%s', $this->getLocale());
            self::$listSearchFields[] = sprintf('alt_%s.keyword', $this->getLocale());
        }

        $result = $this->requestBySearchTerms($searchTerm, self::$listSearchFields);

        return array_map(static function(Result $doc) {
            return $doc->getDocument()->getData()['id'];
        }, $result->getResults());
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
