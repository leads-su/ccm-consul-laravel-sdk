<?php

namespace Consul\Helpers;

use Throwable;

/**
 * Class Str
 *
 * @package Consul\Helpers
 */
class Str
{
    /**
     * Check if given string is actually JSON
     * @param mixed $value
     *
     * @return bool
     */
    public static function isJson(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
