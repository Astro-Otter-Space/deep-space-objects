<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Entity\DTO\ConstellationDTO;
use App\Entity\DTO\DTOInterface;
use App\Entity\ES\ListConstellation;
use AstrobinWs\Response\Image;
use Symfony\Component\Routing\RouterInterface;

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

    public function vignetteView(ConstellationDTO $constellationDTO): array
    {
        /** @var Image $image */
        $image = new Image();
        $image->url_regular = $constellationDTO->getImage();
        $image->user = $constellationDTO->title();
        $image->title = $constellationDTO->title();

        return [
            'id' => $constellationDTO->getId(),
            'value' => $constellationDTO->title(),
            'label' => $constellationDTO->getGeneric(),
            'url' => $constellationDTO->fullUrl(),
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
