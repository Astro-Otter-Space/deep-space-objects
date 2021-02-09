<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\CacheInterface;
use AstrobinWs\Exceptions\WsException;
use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Response\AstrobinError;
use AstrobinWs\Response\AstrobinResponse;
use AstrobinWs\Response\Image;
use AstrobinWs\Response\ListImages;
use AstrobinWs\Services\GetImage;
use App\Classes\Utils;
use JsonException;

/**
 * Class AstrobinService
 * @package Classes
 */
final class AstrobinService
{
    private GetImage $imageWs;

    private CacheInterface $cachePool;

    /**
     * AstrobinService constructor.
     *
     * @param string $astrobinApiKey
     * @param string $astrobinApiSecret
     * @param CacheInterface $cachePool
     */
    public function __construct(string $astrobinApiKey, string $astrobinApiSecret, CacheInterface $cachePool)
    {
        $this->imageWs = new GetImage($astrobinApiKey, $astrobinApiSecret);
        $this->cachePool = $cachePool;
    }


    /**
     * @param string|null $astrobinId
     *
     * @return Image
     */
    public function getAstrobinImage(?string $astrobinId): Image
    {
        $defautImage = new Image();
        $defautImage->url_hd = Utils::IMG_LARGE_DEFAULT;
        $defautImage->url_regular = Utils::IMG_LARGE_DEFAULT;
        $defautImage->user = null;
        $defautImage->title = null;

        try {
            /** @var Image $imageAstrobin */
            $imageAstrobin = (!is_null($astrobinId)) ? $this->imageWs->getById($astrobinId) : basename(Utils::IMG_LARGE_DEFAULT);
            if ($imageAstrobin instanceof AstrobinError) {
                return $defautImage;
            }
            if ($imageAstrobin instanceof AstrobinResponse) {
                return $imageAstrobin;
            }

            return $defautImage;
        } catch(WsResponseException | \Exception $e) {
            return $defautImage;
        }
    }

    /**
     * @param string $subject
     *
     * @return array
     * @throws JsonException
     * @throws \ReflectionException
     */
    public function listImagesBy(string $subject): array
    {
        $tabImages = [];
        $idCache = md5(sprintf('%s_list_images', strtolower($subject)));

        if($this->cachePool->hasItem($idCache)) {
            return unserialize($this->cachePool->getItem($idCache), ['allowed_classes' => Image::class]);
        }

        try {
            /** @var ListImages|Image $listImages */
            $listImages = $this->imageWs->getImagesByTitle($subject, 5);
        } catch (WsResponseException | WsException $e) {
            return [];
        }

        if ($listImages instanceof Image) {
            $tabImages[] = $listImages;

        } elseif ($listImages instanceof ListImages && 0 < $listImages->count) {
            $tabImages = array_map(static function (Image $image) {
                return $image;
            }, iterator_to_array($listImages));
        }

        if (!empty($tabImages)) {
            $this->cachePool->saveItem($idCache, serialize($tabImages));
        }

        return $tabImages;
    }

}
