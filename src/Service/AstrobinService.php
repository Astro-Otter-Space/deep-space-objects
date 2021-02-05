<?php

declare(strict_types=1);

namespace App\Service;

use AstrobinWs\Exceptions\WsResponseException;
use AstrobinWs\Response\AstrobinError;
use AstrobinWs\Response\AstrobinResponse;
use AstrobinWs\Response\Image;
use AstrobinWs\Services\GetImage;
use App\Classes\Utils;

/**
 * Class AstrobinService
 * @package Classes
 */
final class AstrobinService
{
    private GetImage $imageWs;

    /**
     * AstrobinService constructor.
     *
     * @param string $astrobinApiKey
     * @param string $astrobinApiSecret
     */
    public function __construct(string $astrobinApiKey, string $astrobinApiSecret)
    {
        $this->imageWs = new GetImage($astrobinApiKey, $astrobinApiSecret);
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
}
