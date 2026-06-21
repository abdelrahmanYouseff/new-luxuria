<?php

if (! function_exists('translateCarName')) {
    /**
     * Translate / transliterate a car make+model string to Arabic.
     * Falls back to the original string if no mapping is found.
     *
     * Usage in Blade:  {{ translateCarName($vehicle->make . ' ' . $vehicle->model) }}
     */
    function translateCarName(string $name, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale !== 'ar') {
            return $name;
        }

        $makes  = trans('cars.makes');
        $models = trans('cars.models');

        // Work on a lower-cased copy for matching
        $lower = mb_strtolower(trim($name));

        // 1. Try to match the full name as a make key (e.g. "land rover")
        if (isset($makes[$lower])) {
            return $makes[$lower];
        }

        // 2. Replace each known make word in the string
        $result = $name;
        // Sort by length desc so "land rover" is matched before "land"
        uksort($makes, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
        foreach ($makes as $en => $ar) {
            $pattern = '/\b' . preg_quote($en, '/') . '\b/iu';
            if (preg_match($pattern, $result)) {
                $result = preg_replace($pattern, $ar, $result);
            }
        }

        // 3. Replace known model words
        uksort($models, fn($a, $b) => mb_strlen($b) - mb_strlen($a));
        foreach ($models as $en => $ar) {
            $pattern = '/\b' . preg_quote($en, '/') . '\b/iu';
            if (preg_match($pattern, $result)) {
                $result = preg_replace($pattern, $ar, $result);
            }
        }

        return $result;
    }
}
