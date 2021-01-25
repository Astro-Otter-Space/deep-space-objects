<?php

namespace App\Managers;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DsoDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Response\AstrobinResponse;
use AstrobinWs\Response\Image;
use AstrobinWs\Services\GetImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoManager
 * @package App\Manager
 */
class DsoManager
{
    use ManagerTrait;

    private static $listFieldToTranslate = ['catalog', 'type', 'constId', 'astrobin'];

    /** @var DsoRepository  */
    private $dsoRepository;
    /** @var GetImage  */
    private $astrobinImage;
    /** @var UrlGenerateHelper  */
    private $urlGenerateHelper;
    /** @var TranslatorInterface */
    private $translator;
    /** @var CacheInterface */
    private $cacheUtils;
    /** @var  */
    private $locale;
    /** @var DsoDataTransformer */
    private $dsoDataTransformer;
    /** @var ConstellationRepository */
    private $constellationRepository;

    /**
     * DsoManager constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param CacheInterface $cacheUtils
     * @param $locale
     * @param DsoDataTransformer $dsoDataTransformer
     * @param ConstellationRepository $constellationRepository
     */
    public function __construct(DsoRepository $dsoRepository, CacheInterface $cacheUtils, $locale, DsoDataTransformer $dsoDataTransformer, ConstellationRepository $constellationRepository)
    {
        $this->dsoRepository = $dsoRepository;
        $this->astrobinImage = new GetImage();
        $this->cacheUtils = $cacheUtils;
        $this->locale = $locale ?? 'en';
        $this->dsoDataTransformer = $dsoDataTransformer;
        $this->constellationRepository = $constellationRepository;
    }

    /**
     * Build a complete Dso Entity, with Astrobin image and URL
     *
     * @param $id
     *
     * @return \Generator
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function buildDso($id): \Generator
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
            if (!is_null($dso)) {
                // Add astrobin image
                $astrobinImage = $this->getAstrobinImage($dso->getAstrobinId());
                $dso->setAstrobin($astrobinImage);

                // add Constellation
                $constellationDto = $this->constellationRepository
                    ->setLocale($this->locale)
                    ->getObjectById($dso->getConstellationId(), true);

                if ($constellationDto instanceof ConstellationDTO) {
                    $dso->setConstellation($constellationDto);
                }

                //$this->cacheUtils->saveItem($dso->guid(), serialize($dso));
                if ($dso->getAstrobin()->url_hd !== basename(Utils::IMG_DEFAULT)) {
                    //$this->cacheUtils->saveItem($idMd5Cover, serialize($dso->getAstrobin()));
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
    private function getDsoFromCache($idMd5): ?DTOInterface
    {
        $dsoSerialized = $this->cacheUtils->getItem($idMd5);

        /** @var Dso $unserializedDso */
        $unserializedDso = unserialize($dsoSerialized, ['allowed_classes' => DsoDTO::class]);

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
     * @todo move to AstrobinServic
     * Get image (and his owner) from Astrobin
     *
     * @param $astrobinId
     *
     * @return Image
     */
    public function getAstrobinImage($astrobinId): Image
    {
        $defautImage = new Image();
        $defautImage->url_hd = Utils::IMG_LARGE_DEFAULT;
        $defautImage->url_regular = Utils::IMG_LARGE_DEFAULT;
        $defautImage->user = null;
        $defautImage->title = null;

        try {
            /** @var Image $imageAstrobin */
            $imageAstrobin = (!is_null($astrobinId)) ? $this->astrobinImage->getImageById($astrobinId) : basename(Utils::IMG_LARGE_DEFAULT);
            if ($imageAstrobin instanceof AstrobinResponse) {
                return $imageAstrobin;
            }

            return $defautImage;
        } catch(WsResponseException | \Exception $e) {
            return $defautImage;
        }
    }

    /**
     * Get Dso from a constellation identifier and build list
     *
     * @param string $constellationId
     * @param string|null $excludedId
     * @param int $limit
     *
     * @return ListDso
     * @throws WsException
     * @throws \JsonException
     * @throws \ReflectionException
     */
    public function getListDsoFromConst(string $constellationId, ?string $excludedId, int $limit): ListDso
    {
        $listDsoIdByConst = $this->dsoRepository->setLocale($this->locale)->getObjectsByConstId($constellationId, $excludedId, 0, $limit);
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
    private function buildListDso(array $listDsoId): ListDso
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
