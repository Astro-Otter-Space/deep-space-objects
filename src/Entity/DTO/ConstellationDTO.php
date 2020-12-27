<?php

declare(strict_types=1);

namespace App\Entity\DTO;


use App\Entity\ES\Constellation;

/**
 * Class ConstellationDTO
 * @package Entity\DTO
 */
final class ConstellationDTO implements DTOInterface
{
    /**
     * META
     */
    /** @var  */
    private $id;
    /** @var  */
    private $elasticSearchId;
    /** @var  */
    private $fullUrl;
    /** @var  */
    private $locale;

    /** @var Constellation */
    private $constellation;



    public function guid(): string
    {
        // TODO: Implement guid() method.
    }

    public function title(): string
    {
        // TODO: Implement title() method.
    }

    public function fullUrl(): string
    {
        // TODO: Implement fullUrl() method.
    }
}
