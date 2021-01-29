<?php

namespace App\DataTransformer;

use App\Entity\DTO\DTOInterface;

/**
 * Class AbstractDataTransformer
 *
 * @package App\DataTransformer
 */
abstract class AbstractDataTransformer
{
    /**
     * @param DTOInterface $dto
     *
     * @return mixed
     */
    abstract protected function longView(DTOInterface $dto);
}
