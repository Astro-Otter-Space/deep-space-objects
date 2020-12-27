<?php

namespace App\Managers;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\DataTransformer\DsoDataTransformer;
use App\Entity\DTO\DsoDTO;
use App\Entity\ES\Dso;
use App\Entity\ES\ListDso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\ConstellationRepository;
use App\Repository\DsoRepository;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Response\Image;
use AstrobinWs\Services\GetImage;
use Entity\DTO\DTOInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;
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
    private $translatorInterface;
    /** @var CacheInterface */
    private $cacheUtils;
    /** @var  */
    private $locale;
    /** @var DsoDataTransformer */
    private $dsoDataTransformer;

    /**
     * DsoManager constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param UrlGenerateHelper $urlGenerateHelper
     * @param TranslatorInterface $translatorInterface
     * @param CacheInterface $cacheUtils
     * @param $locale
     * @param DsoDataTransformer $dsoDataTransformer
     */
    public function __construct(DsoRepository $dsoRepository, UrlGenerateHelper $urlGenerateHelper, TranslatorInterface $translatorInterface, CacheInterface $cacheUtils, $locale, DsoDataTransformer $dsoDataTransformer)
    {
        $this->dsoRepository = $dsoRepository;
        $this->astrobinImage = new GetImage();
        $this->urlGenerateHelper = $urlGenerateHelper;
        $this->translatorInterface = $translatorInterface;
        $this->cacheUtils = $cacheUtils;
        $this->locale = $locale ?? 'en';
        $this->dsoDataTransformer = $dsoDataTransformer;
    }

    /**
     * Build a complete Dso Entity, with Astrobin image and URL
     *
     * @param $id
     *
     * @return DsoDTO
     * @throws WsException
     * @throws \ReflectionException
     */
    public function buildDso($id): DsoDTO
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
            $dso = $this->dsoRepository->setLocale($this->locale)->getObjectById($id, true);
            if (!is_null($dso)) {

                // Add astrobin image
                $astrobinImage = $this->getAstrobinImage($dso->getAstrobinId());
                $dso->setAstrobin($astrobinImage);

                // Add URl
                //$dso->setFullUrl($this->getDsoUrl($dso, Router::RELATIVE_PATH));

                // add Constellation
                $constellationDto = null; //$this->con
                if (!is_null($constellationDto)) {
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

        return $dso;
    }

    /**
     * @param $idMd5
     *
     * @return Dso|null
     */
    private function getDsoFromCache($idMd5):? Dso
    {
        $dsoSerialized = $this->cacheUtils->getItem($idMd5);

        /** @var Dso $unserializedDso */
        $unserializedDso = unserialize($dsoSerialized);

        return ($unserializedDso instanceof DTOInterface) ? $unserializedDso : null;
    }

    /**
     * Get Dso from a constellation identifier and build list
     *
     * @param DsoDTO $dso
     * @param $limit
     *
     * @return ListDso
     * @throws \ReflectionException
     */
    public function getListDsoFromConst(DsoDTO $dso, $limit): ListDso
    {
        /** @var ListDso $listDso */
        return $this->dsoRepository->setLocale($this->locale)->getObjectsByConstId($dso->getConstellationId(), $dso->getId(), 0, $limit, true);
    }

    /**
     * Format a list of Dso
     * @param $listDso
     * @return array $dataDsoList
     */
    public function buildListDso(ListDso $listDso): array
    {
        /** @var CacheInterface $cacheUtils */
        $cacheUtils = $this->cacheUtils;

        return array_map(function(Dso $dsoChild) use ($cacheUtils) {

            $idCover = md5(sprintf('%s_cover', $dsoChild->getId()));

            if ($cacheUtils->hasItem($idCover)) {
                $imgCached = unserialize($cacheUtils->getItem($idCover));
                $dsoChild->setImage($imgCached);
            } else {
                /** @var Image $imageAstrobin */
                $imageAstrobin = $this->getAstrobinImage($dsoChild->getAstrobinId());
                $dsoChild->setImage($imageAstrobin);
                $cacheUtils->saveItem($idCover, serialize($dsoChild->getImage()));
            }

            return array_merge($this->buildSearchListDso($dsoChild), ['image' => $dsoChild->getImage(), 'filter' => $dsoChild->getType()]);
        }, iterator_to_array($listDso->getIterator()));
    }


    /**
     * @param $searchTerms
     * @param null $typeReturn
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function searchDsoByTerms($searchTerms, $typeReturn = null)
    {
        $resultDso = $this->dsoRepository->setLocale($this->locale)->getObjectsBySearchTerms($searchTerms);

        return array_merge(array_map(function (Dso $dso) use ($typeReturn) {
            if ('id' === $typeReturn) {
                return $dso->getId();
            }

            return $this->buildSearchListDso($dso);

        }, $resultDso));
    }

    /**
     * @todo to check
     * Data returned for autocomplete search
     *
     * @param DsoDTO $dso
     *
     * @return array
     */
    public function buildSearchListDso(DsoDTO $dso): array
    {
        $constellation = ('unassigned' !== $dso->getConstId()) ? $this->translatorInterface->trans('constellation.' . strtolower($dso->getConstId())) : null;
        $title = $dso->title();

        $otherDesigs = $dso->getDesigs();
        $removeDesigs = (is_array($otherDesigs))
            ? array_shift($otherDesigs)
            : null;

        $ajaxValue = (!empty($otherDesigs)) ? sprintf('%s (%s)', $title, implode(Utils::GLUE_DASH, $otherDesigs)) : $title;
        return [
            'id' => $dso->getId(),
            'value' => $title,
            'ajaxValue' => $ajaxValue,
            'subValue' => implode(Utils::GLUE_DASH, $otherDesigs),
            'label' => implode(Utils::GLUE_DASH, array_filter([$this->translatorInterface->trans('type.' . $dso->getType()) , $constellation])),
            'url' => $this->getDsoUrl($dso, Router::ABSOLUTE_PATH)
        ];
    }

    /**
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
            $imageAstrobin = (!is_null($astrobinId)) ? $this->astrobinImage->getById($astrobinId) : basename(Utils::IMG_LARGE_DEFAULT);
            if ($imageAstrobin instanceof Image) {
                return $imageAstrobin;
            }

            return $defautImage;
        } catch(WsResponseException | \Exception $e) {
            return $defautImage;
        }
    }

    /**
     * @todo refactoring
     * @param Dso $dso
     * @param string $typeUrl
     *
     * @return string
     */
    public function getDsoUrl(Dso $dso, string $typeUrl): string
    {
        return $this->urlGenerateHelper->generateUrl($dso, $typeUrl, $this->locale);
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
        $dsoArray = $this->dsoDataTransformer->toArray($dso);
        return $this->formatEntityData($dsoArray, self::$listFieldToTranslate, $this->translatorInterface);
    }

    /**
     * https://discuss.elastic.co/t/elasticsearch-get-random-document-atleast-5-from-each-category/120015
     * @param $limit
     *
     * @return array
     * @throws \Exception
     */
    public function randomDsoWithImages($limit): array
    {
        /**
         * @return \Generator
         */
        $getRandomDso = function () use($limit) {
            yield from $this->dsoRepository->setLocale($this->locale)->getRandomDso($limit);
        };

        /** @var ListDso $listDso */
        $listDso = new ListDso();
        foreach ($getRandomDso() as $dso) {
            $listDso->addDso($dso);
        }

        return $this->buildListDso($listDso);
    }

    /**
     * TODO : move to ConstellationManager
     * @param $constId
     * @return string|null
     */
    public function buildTitleConstellation($constId): ?string
    {
        if (!is_null($constId)) {
            return $this->translatorInterface->trans('constId', ['%count%' => 1]) . ' “' . $this->translatorInterface->trans(sprintf('constellation.%s', strtolower($constId))) . '”';
        }

        return null;
    }

}
