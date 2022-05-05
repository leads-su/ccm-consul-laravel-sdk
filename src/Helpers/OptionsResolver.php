<?php

namespace Consul\Helpers;

use Illuminate\Support\Arr;

/**
 * Class OptionsResolver
 *
 * @package Consul\Helpers
 */
final class OptionsResolver
{
    /**
     * Resolve options
     * @param array $options
     * @param array $availableOptions
     *
     * @return array
     */
    public static function resolve(array $options = [], array $availableOptions = []): array
    {
        foreach ($options as $key => $value) {
            if (is_int($key)) {
                unset($options[$key]);
                Arr::set($options, $value, $value);
            }
        }
        $filteredOptions = array_filter($options, static function (string $key) use ($availableOptions): bool {
            return in_array($key, $availableOptions, true);
        }, ARRAY_FILTER_USE_KEY);

        $resolvedOptions = [];

        foreach ($filteredOptions as $key => $value) {
            if ($key === $value) {
                // We are assuming that when parameter key equals to value, it is a boolean parameter set to TRUE
                Arr::set($resolvedOptions, $value, true);
            } else {
                Arr::set($resolvedOptions, $key, $value);
            }
        }

        return $resolvedOptions;
    }
}
