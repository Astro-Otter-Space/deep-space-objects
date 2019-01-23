<?php


namespace App\Managers;


use App\Classes\Utils;
use App\Entity\Dso;
use App\Entity\ListDso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\DsoRepository;
use Astrobin\Exceptions\WsResponseException;
use Astrobin\Response\Image;
use Astrobin\Services\GetImage;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DsoManager
 * @package App\Manager
 */
class DsoManager
{

    private static $listFieldToTranslate = ['catalog', 'type', 'constId'];

    /** @var DsoRepository  */
    private $dsoRepository;
    /** @var GetImage  */
    private $astrobinImage;
    /** @var UrlGenerateHelper  */
    private $urlGenerateHelper;
    /** @var TranslatorInterface */
    private $translatorInterface;

    /**
     * DsoManager constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param UrlGenerateHelper $urlGenerateHelper
     * @param TranslatorInterface $translatorInterface
     */
    public function __construct(DsoRepository $dsoRepository, UrlGenerateHelper $urlGenerateHelper, TranslatorInterface $translatorInterface)
    {
        $this->dsoRepository = $dsoRepository;
        $this->astrobinImage = new GetImage();
        $this->urlGenerateHelper = $urlGenerateHelper;
        $this->translatorInterface = $translatorInterface;
    }


    /**
     * Build a complete Dso Entity
     * @param $id
     * @return Dso
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function buildDso($id): Dso
    {
        /** @var Dso $dso */
        $dso = $this->dsoRepository->getObjectById($id);

        // Add astrobin image
        $astrobinImage = $this->getAstrobinImage($dso->getAstrobinId(), $dso->getId());
        $dso->setImage($astrobinImage);

        // Add URl
        $dso->setFullUrl($this->getDsoUrl($dso));

        return $dso;
    }


    /**
     * @param Dso $dso
     * @param $limit
     * @return array
     * @throws \ReflectionException
     */
    public function buildListDso(Dso $dso, $limit): array
    {
        /** @var ListDso $listDso */
        $listDso = $this->dsoRepository->getObjectsByConstId($dso->getConstId(), $limit);

        /** @var GetImage $astrobinImage */
        $astrobinImage = new GetImage();
        $dsoList = array_map(function(Dso $dsoChild) use ($astrobinImage) {
            $imageAstrobin = null;
            $imageAstrobin = (!is_null($dsoChild->getAstrobinId())) ? $astrobinImage->getImageById($dsoChild->getAstrobinId()) : $astrobinImage->getImagesBySubject($dsoChild->getId(), 1);
            if (!is_null($imageAstrobin) && $imageAstrobin instanceof Image) {
                $imageAstrobin = $imageAstrobin->url_regular;
            }

            return array_merge($this->buildSearchData($dsoChild), ['image' => $imageAstrobin]);
        }, iterator_to_array($listDso->getIterator()));

        return $dsoList;
    }


    /**
     * Data returned for autocomplete search
     *
     * @param Dso $dso
     * @return array
     */
    public function buildSearchData(Dso $dso): array
    {
        $constellation = ('unassigned' !== $dso->getConstId()) ?$this->translatorInterface->trans('const_id.' . strtolower($dso->getConstId())) : null;

        return [
            'id' => $dso->getId(),
            'value' => $this->buildTitle($dso),
            'label' => implode(Utils::GLUE_DASH, array_filter([$this->translatorInterface->trans('type.' . $dso->getType()) , $constellation])),
            'url' => $this->getDsoUrl($dso)
        ];
    }

    /**
     * Get image from Astrobin
     *
     * @param $astrobinId
     * @param $id
     * @param string $param
     * @return string|null
     * @throws \Astrobin\Exceptions\WsException
     * @throws \ReflectionException
     */
    public function getAstrobinImage($astrobinId, $id,  $param = 'url_hd')
    {
        try {
            /** @var Image $imageAstrobin */
            $imageAstrobin = (!is_null($astrobinId)) ? $this->astrobinImage->getImageById($astrobinId) : $this->astrobinImage->getImagesBySubject($id, 1);
            if (!is_null($imageAstrobin) && $imageAstrobin instanceof Image) {
                return $imageAstrobin->$param;
            }
        } catch(WsResponseException $e) {
            return null;
        }
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
        /** @var TranslatorInterface $translate */
        $translate = $this->translatorInterface;
        $listFields = self::$listFieldToTranslate;

        $dsoToArray = $dso->toArray();

        $serialize = array_map(function($value, $key) use($translate, $listFields) {
            return [
                'col0' => $translate->trans($key, ['%count%' => 1]),
                'col1' => (in_array($key, $listFields)) ? $translate->trans($value, ['%count%' => 1]): $value
            ];
        }, $dsoToArray, array_keys($dsoToArray));

        return $serialize;
    }


    /**
     * Return a formated title
     *
     * @param Dso $dso
     * @return string
     */
    public function buildTitle(Dso $dso): string
    {
        // Fist we retrieve desigs
        $desig = (is_array($dso->getDesigs())) ? current($dso->getDesigs()) : $dso->getDesigs();
        // If Alt is set, we merge desig and alt
        $title = (empty($dso->getAlt())) ? $desig : implode (Dso::DATA_CONCAT_GLUE, [$dso->getAlt(), $desig]);

        // If title still empty, we put Id
        $title = (empty($title))? $dso->getId() : $title;

        return $title;
    }


    /**
     * @param Dso $dso
     * @return string
     */
    public function buildgeoJson(Dso $dso): string
    {
        $data = [
            "type" => "Feature",
            "geometry" => $dso->getGeometry(),
            "properties" => [
                "name" => $this->buildTitle($dso)
            ]
        ];

        return json_encode($data);
    }
}
