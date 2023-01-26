<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\ListConstellation;
use AstrobinWs\Response\Image;

/**
 * Class CollectionDataTransformer
 * @package App\DataTransformer
 */
final class ConstellationDataTransformer extends AbstractDataTransformer
{

    /**
     * @param ListConstellation $listConstellation
     *
     * @return array
     */
    public function listVignettesView(ListConstellation $listConstellation): array
    {
        $cards = [];
        foreach ($listConstellation as $constellationDTO) {
            $cards[] = $this->vignetteView($constellationDTO);
        }

        return $cards;
    }


    /**
     * @param ConstellationDTO $constellationDTO
     *
     * @return array
     */
    public function vignetteView(ConstellationDTO $constellationDTO): array
    {
        $image = new Image();
        $image->url_regular = $constellationDTO->getImage();
        $image->user = $constellationDTO->title();
        $image->title = $constellationDTO->title();

        return [
            'id' => $constellationDTO->getId(),
            'value' => $constellationDTO->title(),
            'label' => $constellationDTO->getGeneric(),
            'ajaxValue' => $constellationDTO->title(),
            'url' => $constellationDTO->relativeUrl(),
            'image' => $image,
            'filter' => $constellationDTO->getKind()
        ];
    }

    /**
     * @inheritDoc
     */
    protected function longView(DTOInterface $dto)
    {
        // TODO: Implement longView() method.
    }
}
