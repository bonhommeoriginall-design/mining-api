<?php

namespace App\Services;

class DocumentTextNormalizer
{
    public static function normalize(string $raw): string
    {
        if (trim($raw) === '') {
            return '';
        }

        $text = preg_replace('/<[^>]+>/', ' ', $raw) ?? $raw;
        $text = str_replace("\u{00A0}", ' ', $text);
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $text = trim($text);

        $text = preg_replace_callback(
            '/([a-zàâäéèêëïîôöùûüç])([A-ZÀÂÄÉÈÊËÏÎÔÖÙÛÜÇ])/u',
            static fn (array $m) => $m[1].' '.$m[2],
            $text,
        ) ?? $text;

        $text = preg_replace_callback(
            '/([.;:,!?])([A-Za-zÀ-ÿ0-9])/u',
            static fn (array $m) => $m[1].' '.$m[2],
            $text,
        ) ?? $text;

        return trim($text);
    }
}
