<?php

declare(strict_types=1);

namespace App\Entity\DTO;

interface DTOInterface
{
    public function guid(): string;
    public function title(): string;
    public function relativeUrl(): string;
    public function absoluteUrl(): ?string;
    public function setRelativeUrl(string $url): DTOInterface;
    public function setAbsoluteUrl(string $url): DTOInterface;
    public function getLocale(): string;
}
