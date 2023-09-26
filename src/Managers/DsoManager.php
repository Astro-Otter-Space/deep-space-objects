<?php

declare(strict_types=1);

namespace App\Managers;

use App\Service\Cache\CachePoolInterface;
use App\Classes\Utils;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Constellation;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use App\Service\AstrobinService;
use App\Service\InjectionTrait\SymfonyServicesTrait;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Response\DTO\AstrobinError;
use AstrobinWs\Response\DTO\AstrobinResponse;
use AstrobinWs\Response\DTO\Item\Image;
use AstrobinWs\Response\DTO\Item\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoManager
 * @package App\Manager
 */
class DsoManager
{
    use ManagerTrait, SymfonyServicesTrait;

    private static array $listFieldToTranslate = ['catalog', 'type', 'constId', 'astrobin'];

    private DsoRepository $dsoRepository;
    private AstrobinService $astrobinService;
    private UrlGenerateHelper $urlGenerateHelper;
    private CachePoolInterface $cacheUtils;
    private string $locale;
    private DsoDataTransformer $dsoDataTransformer;
    private ConstellationRepository $constellationRepository;

    /**
     * DsoManager constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param CachePoolInterface $cacheUtils
     * @param string|null $locale
     * @param DsoDataTransformer $dsoDataTransformer
     * @param ConstellationRepository $constellationRepository
     * @param AstrobinService $astrobinService
     * @param $
     */
    public function __construct(
        DsoRepository $dsoRepository,
        CachePoolInterface $cacheUtils,
        ?string $locale,
        DsoDataTransformer $dsoDataTransformer,
        ConstellationRepository $constellationRepository,
        AstrobinService $astrobinService
    )
    {
        $this->dsoRepository = $dsoRepository;
        $this->astrobinService = $astrobinService;
        $this->cacheUtils = $cacheUtils;
        $this->locale = $locale ?? 'en';
        $this->dsoDataTransformer = $dsoDataTransformer;
        $this->constellationRepository = $constellationRepository;
    }

    /**
     * @return DsoRepository
     */
    public function getDsoRepository(): DsoRepository
    {
        return $this->dsoRepository;
    }


    /**
     * Build a complete Dso Entity, with Astrobin image and URL
     *
     * @param string $id
     *
     * @return \Generator
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    private function buildDso(string $id): \Generator
    {
        $idMd5 = md5(sprintf('%s_%s', $id, $this->locale));
        $idMd5Cover = md5(sprintf('%s_cover', $id));

        if ($this->cacheUtils->hasItem($idMd5)) {
            $dsoFromCache = $this->getDsoFromCache($idMd5);

            if (is_null($dsoFromCache)) {
                $this->cacheUtils->deleteItem($idMd5);
                $dso = $this->buildDso($id);
            } else {
                $dso = $dsoFromCache;
            }
        } else {
            /** @var DsoDTO|DTOInterface $dso */
            $dso = $this->dsoRepository->setLocale($this->locale)->getObjectById($id);
            $dso->setTypeLabel($this->translator->trans($dso->getType()));
            if (!is_null($dso)) {
                // Add astrobin image
                $astrobinImage = $this->astrobinService->getAstrobinImage($dso->getAstrobinId());
                $dso->setAstrobin($astrobinImage);

                // add astrobin user
                if ($astrobinImage->user) {
                    $astrobinUser = $this->astrobinService->getAstrobinUser($astrobinImage->user);
                    $dso->setAstrobinUser($astrobinUser);
                } else {
                    $dso->setAstrobinUser(null);
                }

                // add Constellation
                $constellationDto = $this->constellationRepository
                    ->setLocale($this->locale)
                    ->getObjectById($dso->getConstellationId());

                if ($constellationDto instanceof ConstellationDTO) {
                    $dso->setConstellation($constellationDto);
                }

                $this->cacheUtils->saveItem($idMd5, serialize($dso));

                if ($dso->getAstrobin()->url_hd !== basename(Utils::IMG_DEFAULT)) {
                    $this->cacheUtils->saveItem($idMd5Cover, serialize($dso->getAstrobin()));
                }
            } else {
                throw new NotFoundHttpException(sprintf("DSO ID %s not found", $id));
            }
        }

        yield $dso;
    }

    /**
     * @param $idMd5
     *
     * @return Dso|null
     */
    public function getDsoFromCache($idMd5): ?DTOInterface
    {
        $dsoSerialized = $this->cacheUtils->getItem($idMd5);
        if (is_null($dsoSerialized)) {
            return null;
        }

        $unserializedDso = unserialize($dsoSerialized, [
            'allowed_classes' => [
                DsoDTO::class,
                ConstellationDTO::class,
                Image::class,
                User::class,
                Dso::class,
                Constellation::class,
                AstrobinError::class
            ]
        ]);

        return ($unserializedDso instanceof DTOInterface) ? $unserializedDso : null;
    }


    /**
     * Translate data vor display in VueJs
     *
     * @param DsoDTO $dso
     *
     * @return array
     */
    public function formatVueData(DsoDTO $dso): array
    {
        return $this->dsoDataTransformer->buildTableData($dso, self::$listFieldToTranslate);
    }

    /**
     * @param $id
     *
     * @return DTOInterface
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function getDso(string $id): DTOInterface
    {
        $getDso = function($id) {
            yield from $this->buildDso($id);
        };

        return $getDso($id)->current();
    }

    /**
     * Get Dso from a constellation identifier and build list
     *
     * @param string $constellationId
     * @param string|null $excludedId
     * @param int $offset
     * @param int $limit
     *
     * @return ListDso
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function getListDsoFromConst(string $constellationId, ?string $excludedId, int $offset, int $limit): ListDso
    {
        $listDsoIdByConst = $this->dsoRepository
            ->setLocale($this->locale)
            ->getObjectsByConstId($constellationId, $excludedId, $offset, $limit);

        return $this->buildListDso($listDsoIdByConst);
    }

    /**
     * @return ListDso
     * @throws WsException
     * @throws \ReflectionException|\JsonException
     */
    public function getListDsoLastUpdated(): ListDso
    {
        $listDsoIdLastUpdated = $this->dsoRepository->getLastUpdated();
        return $this->buildListDso($listDsoIdLastUpdated);
    }


    /**
     * Get list Dso by search terms
     *
     * @param string $searchTerms
     * @param string|null $typeReturn >
     *
     * @return mixed
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function searchDsoByTerms(string $searchTerms, ?string $typeReturn): ListDso
    {
        $resultDsoId = $this->dsoRepository->setLocale($this->locale)->getObjectsBySearchTerms($searchTerms);
        return $this->buildListDso($resultDsoId);
    }

    /**
     * Get random with Dso where AstrobinId is not null
     * @param int $limit
     *
     * @return ListDso
     * @throws WsException
     * @throws \ReflectionException|\JsonException
     *
     * https://discuss.elastic.co/t/elasticsearch-get-random-document-atleast-5-from-each-category/120015
     */
    public function randomDsoWithImages(int $limit): ListDso
    {
        $randomDsoId = $this->dsoRepository->setLocale($this->locale)->getRandomDso($limit);
        return $this->buildListDso($randomDsoId);
    }

    /**
     * @param array $listDsoId
     *
     * @return ListDso
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function buildListDso(array $listDsoId): ListDso
    {
        $getDso = function () use ($listDsoId) {
            foreach ($listDsoId as $dsoId) {
                yield from $this->buildDso($dsoId);
            }
        };

        $listDso = new ListDso();
        foreach ($getDso() as $dso) {
            $listDso->addDso($dso);
        }
        return $listDso;
    }
}
