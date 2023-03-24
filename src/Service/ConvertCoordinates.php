<?php

namespace App\Service;

use App\Command\ConvertCoordinatesCommand;

final class ConvertCoordinates
{

    public function __invoke(string $type, string $value): ?float
    {
        return match ($type) {
            'raToLon' => $this->raToLon($value),
            'decToLat' => $this->decToLat($value),
            'default' => $value
        };
    }

    private function raToLon(string $ra): ?float
    {
        preg_match_all(ConvertCoordinatesCommand::REGEX, $ra, $matches, PREG_PATTERN_ORDER);
        $h = (float)$matches[0][0];
        $mn = (float)$matches[0][1];
        $sec = (float)$matches[0][2];

        $lon = ($h + ($mn/60) + ($sec/3600))*15;
        return ($lon > 180) ? $lon-360 : $lon;
    }

    public function decToLat(string $dec): ?float
    {
        preg_match_all(ConvertCoordinatesCommand::REGEX, $dec, $matches, PREG_PATTERN_ORDER);
        $deg = (float)$matches[0][0];
        $isNegative = $deg < 0;

        $deg = (float)str_replace('-', '', $matches[0][0]);
        $mn = (float)$matches[0][1];
        $sec = (float)$matches[0][2];

        $lat = $deg + $mn/60 + $sec/3600;

        $lat = ($isNegative)? '-' . $lat: $lat;

        return (float)$lat;
    }
}
