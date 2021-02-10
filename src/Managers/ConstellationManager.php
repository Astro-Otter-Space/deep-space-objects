<?php

namespace App\Managers;

use App\Entity\DTO\ConstellationDTO;
use App\Entity\ES\ListConstellation;
use App\Repository\ConstellationRepository;

/**
 * Class ConstellationManager
 * @package App\Managers
 */
class ConstellationManager
{
    /** @var ConstellationRepository  */
    private ConstellationRepository $constellationRepository;
    /** @var string */
    private string $locale;

    /**
     * ConstellationManager constructor.
     *
     * @param ConstellationRepository $constellationRepository
     * @param string $locale
     */
    public function __construct(ConstellationRepository $constellationRepository, string $locale)
    {
        $this->constellationRepository = $constellationRepository;
        $this->locale = $locale;
    }


    /**
     * Build a constellation entity from ElasticSearch request by $id
     *
     * @param string $id
     *
     * @return \Generator
     * @throws \JsonException
     */
    private function buildConstellation(string $id): \Generator
    {
        /** @var ConstellationDTO $constellation */
        $constellation = $this->constellationRepository
            ->setlocale($this->locale)
            ->getObjectById($id);

        yield $constellation;
    }


    /**
     * Get all constellation and build formated data for template
     *
     * @param array $listConstIds
     *
     * @return ListConstellation
     * @throws \JsonException
     */
    private function buildListConstellation(array $listConstIds): ListConstellation
    {
        $getConstellation = function() use($listConstIds){
            foreach ($listConstIds as $constellationId) {
                yield from $this->buildConstellation($constellationId);
            }
        };

        $listConstellation = new ListConstellation();
        /** @var ConstellationDTO $constellation */
        foreach ($getConstellation() as $constellation) {
            $listConstellation->addConstellation($constellation);
        }

        return $listConstellation;
    }


    /**
     * Build search by terms
     *
     * @param $searchTerms
     *
     * @return mixed
     * @throws \JsonException
     */
    public function searchConstellationsByTerms($searchTerms): ListConstellation
    {
        $resultConstellationsId = $this->constellationRepository->setLocale($this->locale)->getConstellationsBySearchTerms($searchTerms);
        return $this->buildListConstellation($resultConstellationsId);
    }


    /**
     * @return ListConstellation
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function getAllConstellations(): ListConstellation
    {
        $resultAllConstellation = $this->constellationRepository->setLocale($this->locale)->getAllConstellation();
        return $this->buildListConstellation($resultAllConstellation);
    }

    /**
     * @param string $id
     *
     * @return ConstellationDTO
     * @throws \JsonException
     */
    public function getConstellationById(string $id): ?ConstellationDTO
    {
        $getConstellation = function($id) {
            yield from $this->buildConstellation($id);
        };

        return $getConstellation($id)->current();
    }
}
