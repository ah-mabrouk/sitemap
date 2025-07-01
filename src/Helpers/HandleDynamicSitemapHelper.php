<?php

namespace SolutionPlus\Sitemap\Helpers;

use Illuminate\Database\Eloquent\Collection;

class HandleDynamicSitemapHelper
{
    public static function handleTranslations(Collection $modelItems, array $translatedSegments, $routeKeyName = 'slug', $priority = 0.8): array
    {
        $urls = [];

        $defaultLocale = config('sitemap.default_locale');

        $locales = config('translatable.locales');

        foreach ($modelItems as $modelItem) {
            $translations = $modelItem->translations()->pluck($routeKeyName, 'locale')->toArray();

            // Ensure default locale exists in the map
            if (!isset($translations[$defaultLocale])) {
                $defaultSlug = $modelItem->$routeKeyName; // fallback if only one translation exists
                $translations[$defaultLocale] = $defaultSlug;
            }

            // Build one entry for each available locale translation
            foreach ($translations as $locale => $slug) {
                $alternates = [];

                foreach ($locales as $altLocale) {
                    // Use existing or fallback to default
                    $altSlug = $translations[$altLocale] ?? $translations[$defaultLocale];
                    $segment = $translatedSegments[$altLocale];

                    $href = $altLocale . '/' . $segment . '/' . $altSlug;

                    $alternates[] = [
                        'hreflang' => $altLocale,
                        'href' => $href,
                    ];
                }

                // Add x-default
                $xDefaultSegment = $translatedSegments[$defaultLocale];
                $xDefaultSlug = $translations[$defaultLocale];
                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => $xDefaultSegment . '/' . $xDefaultSlug,
                ];

                $segment = $translatedSegments[$locale];

                $urls[] = [
                    'loc' => $locale . '/' . $segment . '/' . $slug,
                    'other_locs' => [],
                    'alternates' => $alternates,
                    'priority' => $priority,
                ];
            }
        }

        return $urls;
    }

    public static function handleNonTranslations(Collection $modelItems, array $translatedSegments, $routeKeyName = 'slug', $priority = 0.8): array
    {
        $urls = [];
        $defaultLocale = config('sitemap.default_locale');
        $locales = config('translatable.locales');

        foreach ($modelItems as $modelItem) {
            $routeValue = $modelItem->{$routeKeyName}; // this is constant across locales

            foreach ($locales as $locale) {
                $segment = $translatedSegments[$locale] ?? $translatedSegments[$defaultLocale];

                $alternates = [];

                foreach ($locales as $altLocale) {
                    $altSegment = $translatedSegments[$altLocale] ?? $translatedSegments[$defaultLocale];

                    $alternates[] = [
                        'hreflang' => $altLocale,
                        'href' => $altLocale . '/' . $altSegment . '/' . $routeValue,
                    ];
                }

                // Add x-default
                $xDefaultSegment = $translatedSegments[$defaultLocale];
                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => $xDefaultSegment . '/' . $routeValue,
                ];

                $urls[] = [
                    'loc' => $locale . '/' . $segment . '/' . $routeValue,
                    'other_locs' => [],
                    'alternates' => $alternates,
                    'priority' => $priority,
                ];
            }
        }

        return $urls;
    }
}
