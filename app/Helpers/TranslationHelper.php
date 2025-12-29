<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationHelper
{
    public static function translate(?string $text, string $locale): string
{
    if (empty($text)) {
        return '';
    }

    if ($locale === 'hi') {
        try {
            return (new GoogleTranslate($locale))->translate($text);
        } catch (\Exception $e) {
            return $text;
        }
    }

    return $text;
}


    public static function getLocalizedRoute(string $slug, string $locale): string
    {
        return $locale === 'hi'
            ? route('left-menu.show.hi', $slug)
            : route('left-menu.show', $slug);
    }
}
