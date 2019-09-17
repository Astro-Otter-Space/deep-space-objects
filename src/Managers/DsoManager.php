<?php

namespace App\Managers;

use App\Classes\CacheInterface;
use App\Classes\Utils;
use App\Entity\Dso;
use App\Entity\ListDso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\DsoRepository;
use Astrobin\Exceptions\WsException;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Response\Image;
use Astrobin\Services\GetImage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoManager
 * @package App\Manager
 */
class DsoManager
{

    use ManagerTrait;

    private static $listFieldToTranslate = ['catalog', 'type', 'constId'];

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

    /**
     * DsoManager constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param UrlGenerateHelper $urlGenerateHelper
     * @param TranslatorInterface $translatorInterface
     * @param CacheInterface $cacheUtils
     * @param string $locale
     */
    public function __construct(DsoRepository $dsoRepository, UrlGenerateHelper $urlGenerateHelper, TranslatorInterface $translatorInterface, CacheInterface $cacheUtils, $locale)
    {
        $this->dsoRepository = $dsoRepository;
        $this->astrobinImage = new GetImage();
        $this->urlGenerateHelper = $urlGenerateHelper;
        $this->translatorInterface = $translatorInterface;
        $this->cacheUtils = $cacheUtils;
        $this->locale = $locale;
    }


    /**
     * Build a complete Dso Entity, with Astrobin image and URL
     *
     * @param $id
     *
     * @return Dso
     * @throws WsException
     * @throws \ReflectionException
     */
    public function buildDso($id): Dso
    {
        $idMd5 = md5(sprintf('%s_%s', $id, $this->locale));
        $idMd5Cover = md5(sprintf('%s_cover', $id));

        if ($this->cacheUtils->hasItem($idMd5)) {
            $dsoSerialized = $this->cacheUtils->getItem($idMd5);
            /** @var Dso $dso */
            $dso = unserialize($dsoSerialized);
        } else {
            /** @var Dso $dso */
            $dso = $this->dsoRepository->setLocale($this->locale)->getObjectById($id);
            if (!is_null($dso)) {

                // Add astrobin image
                list($astrobinImageUrl, $astrobinImageUser) = $this->getAstrobinImage($dso->getAstrobinId(), $dso->getId());
                $dso->setImage($astrobinImageUrl);
                $dso->setAstrobinUser($astrobinImageUser);

                // Add URl
                $dso->setFullUrl($this->getDsoUrl($dso));

                $this->cacheUtils->saveItem($idMd5, serialize($dso));
                if ($dso->getImage() !== basename(Utils::IMG_DEFAULT)) {
                    $this->cacheUtils->saveItem($idMd5Cover, serialize($dso->getImage()));
                }
            } else {
                throw new NotFoundHttpException(sprintf("DSO ID %s not found", $id));
            }
        }

        return $dso;
    }


    /**
     * Get Dso from a constellation identifier and build list
     *
     * @param Dso $dso
     * @param $limit
     * @return ListDso
     * @throws \ReflectionException
     */
    public function getListDsoFromConst(Dso $dso, $limit)
    {
        /** @var ListDso $listDso */
        return $this->dsoRepository->setLocale($this->locale)->getObjectsByConstId($dso->getConstId(), $dso->getId(), 0, $limit);
    }

    /**
     * Format a list of Dso
     * @param $listDso
     * @return array $dataDsoList
     */
    public function buildListDso(ListDso $listDso): array
    {
        /** @var GetImage $astrobinImage */
        $astrobinImage = new GetImage();
        /** @var CacheInterface $cacheUtils */
        $cacheUtils = $this->cacheUtils;

        return array_map(function(Dso $dsoChild) use ($astrobinImage, $cacheUtils) {

            $imgUrl = Utils::IMG_DEFAULT;
            $astrobinUser = '';
            $idCover = md5(sprintf('%s_cover', $dsoChild->getId()));

            if ($cacheUtils->hasItem($idCover)) {
                $imgUrl = unserialize($cacheUtils->getItem($idCover));

            } else {
                /** @var Image $imageAstrobin */
                $imageAstrobin = (!is_null($dsoChild->getAstrobinId())) ? $astrobinImage->getImageById($dsoChild->getAstrobinId()) : Utils::IMG_DEFAULT;
                if (!is_null($imageAstrobin) && $imageAstrobin instanceof Image) {
                    $imgUrl = $imageAstrobin->url_regular;
                    $astrobinUser = $imageAstrobin->user;
                }
                $cacheUtils->saveItem($idCover, serialize($imgUrl));
            }

            return array_merge($this->buildSearchListDso($dsoChild), ['image' => $imgUrl, 'user' => $astrobinUser, 'filter' => $dsoChild->getType()]);
        }, iterator_to_array($listDso->getIterator()));
    }


    /**
     * @param $searchTerms
     * @param null $typeReturn
     *
     * @return mixed
     */
    public function searchDsoByTerms($searchTerms, $typeReturn = null)
    {
        $resultDso = $this->dsoRepository->setLocale($this->locale)->getObjectsBySearchTerms($searchTerms);

        return call_user_func("array_merge", array_map(function(Dso $dso) use ($typeReturn) {
            if ('id' === $typeReturn) {
                return $dso->getId();
            } else {
                return $this->buildSearchListDso($dso);
            }

        }, $resultDso));
    }

    /**
     * Data returned for autocomplete search
     *
     * @param Dso $dso
     * @return array
     */
    public function buildSearchListDso(Dso $dso): array
    {
        $constellation = ('unassigned' !== $dso->getConstId()) ? $this->translatorInterface->trans('constellation.' . strtolower($dso->getConstId())) : null;
        $title = $this->buildTitle($dso);

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
            'url' => $this->getDsoUrl($dso)
        ];
    }

    /**
     * Get image (and his owner) from Astrobin
     *
     * @param $astrobinId
     * @param $id
     * @param string $param
     *
     * @return array
     *
     * @throws WsException
     * @throws \ReflectionException
     */
    public function getAstrobinImage($astrobinId, $id, $param = 'url_hd'): array
    {
        try {
            /** @var Image $imageAstrobin */
            $imageAstrobin = (!is_null($astrobinId)) ? $this->astrobinImage->getImageById($astrobinId) : basename(Utils::IMG_DEFAULT);
            if (!is_null($imageAstrobin) && $imageAstrobin instanceof Image) {
                return [$imageAstrobin->$param, $imageAstrobin->user];
            }
        } catch(WsResponseException $e) {
            return [basename(Utils::IMG_DEFAULT), ''];
        }
        return [basename(Utils::IMG_DEFAULT), ''];
    }

    /**
     * @param Dso $dso
     * @return string
     */
    public function getDsoUrl(Dso $dso)
    {
        return $this->urlGenerateHelper->generateUrl($dso);
    }

    /**
     * Translate data vor display in VueJs
     *
     * @param Dso $dso
     * @return array
     */
    public function formatVueData(Dso $dso): array
    {
        return $this->formatEntityData($dso, self::$listFieldToTranslate, $this->translatorInterface);
    }


    /**
     * Return a formated title
     *
     * @param Dso $dso
     * @return string
     */
    public function buildTitle(Dso $dso): string
    {
        // Fist we retrieve desigs and other desigs
        $desig = (is_array($dso->getDesigs())) ? current($dso->getDesigs()) : $dso->getDesigs();
//        $otherDesigs = (is_array($dso->getDesigs())) ? array_shift($dso->getDesigs()): '';

        // If Alt is set, we merge desig and alt
        $title = (empty($dso->getAlt()))
            ? $desig
            : implode (Dso::DATA_CONCAT_GLUE, [$dso->getAlt(), $desig]);

        // If title still empty, we put Id
        $title = (empty($title))
            ? $dso->getId()
            : $title;

        return $title;
    }


    /**
     * TODO : move to ConstellationManager
     * @param $constId
     * @return string|null
     */
    public function buildTitleConstellation($constId)
    {
        if (!is_null($constId)) {
            return $this->translatorInterface->trans('constId', ['%count%' => 1]) . ' “' . $this->translatorInterface->trans(sprintf('constellation.%s', strtolower($constId))) . '”';
        } else {
            return null;
        }
    }

    /**
     * @param Dso $dso
     * @return array
     */
    public function buildgeoJson(Dso $dso): array
    {
        return [
            "type" => "Feature",
            "id" => $dso->getId(),
            "geometry" => $dso->getGeometry(),
            "properties" => [
                "name" => $this->buildTitle($dso),
                "type" => $dso->getType(),
                "mag" => $dso->getMag()
            ]
        ];
    }
}
