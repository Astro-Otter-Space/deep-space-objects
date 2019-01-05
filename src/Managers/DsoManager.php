<?php


namespace App\Managers;


use App\Entity\Dso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\DsoRepository;
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

        // Add image
        if ($dso->getAstrobinId()) {
            $imageAstrobin = $this->astrobinImage->getImageById($dso->getAstrobinId());
            $dso->setImage($imageAstrobin->url_hd);
        } else {
            $imageAstrobin = $this->astrobinImage->getImagesBySubject($dso->getId(), 1);
            if (!is_null($imageAstrobin)) {
                $dso->setImage($imageAstrobin->url_hd);
            }
        }

        // Add URl
        $dso->setFullUrl($this->urlGenerateHelper->generateUrl($dso));

        return $dso;
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
        $array = $dso->toArray();
        $listFields = self::$listFieldToTranslate;

        $serialize = array_map(function($value, $key) use($translate, $listFields) {
            return [
                'col0' => $translate->trans($key, ['%count%' => 1]),
                'col1' => (in_array($key, $listFields)) ? $translate->trans($value, ['%count%' => 1]): $value
            ];
        }, $dso->toArray(), array_keys($dso->toArray()));

        return $serialize;
    }
}
