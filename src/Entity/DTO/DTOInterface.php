<?php

declare(strict_types=1);

namespace Entity\DTO;

interface DTOInterface
{
    public function guid(): string;
    public function title(): string;
    public function fullUrl(): string;
}
