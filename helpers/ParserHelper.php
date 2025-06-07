<?php

namespace App\Helpers;
class ParserHelper
{
    public static function processPrice(?string $value): ?int
    {
        if (!$value) return null;
        $cleaned = str_replace([',', ' '], '', $value);
        if (!is_numeric($cleaned)) return null;
        return intval($cleaned) / 10;
    }

    public static function processChange(?string $value): ?float
    {
        if (!$value) return null;
        if (preg_match('/\(([\d.]+)%\)/', $value, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
