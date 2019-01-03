<?php


namespace App\Managers;


use App\Entity\Dso;
use App\Helpers\UrlGenerateHelper;
use App\Repository\DsoRepository;
use Astrobin\Services\GetImage;

/**
 * Class DsoManager
 * @package App\Manager
 */
class DsoManager
{

    /** @var DsoRepository  */
    private $dsoRepository;
    /** @var GetImage  */
    private $astrobinImage;
    /** @var UrlGenerateHelper  */
    private $urlGenerateHelper;

    /**
     * DsoManager constructor.
     *
     * @param DsoRepository $dsoRepository
     * @param UrlGenerateHelper $urlGenerateHelper
     */
    public function __construct(DsoRepository $dsoRepository,UrlGenerateHelper $urlGenerateHelper)
    {
        $this->dsoRepository = $dsoRepository;
        $this->astrobinImage = new GetImage();
        $this->urlGenerateHelper = $urlGenerateHelper;
    }


    /**
     * Build a complete Dso Entity
     * @param $id
     * @return null
     * @throws \Astrobin\Exceptions\WsException
     * @throws \Astrobin\Exceptions\WsResponseException
     * @throws \ReflectionException
     */
    public function buildDso($id)
    {
        /** @var Dso $dso */
        $dso = $this->dsoRepository->getObjectById($id);

        // Add image
        if ($dso->getAstrobinId()) {
            $imageAstrobin = $this->astrobinImage->getImageById($dso->getAstrobinId());
            $dso->setImage($imageAstrobin->url_hd);
        }

        // Add URl
        $dso->setFullUrl($this->urlGenerateHelper->generateUrl($dso));

        return $dso;
    }
}
