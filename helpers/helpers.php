<?php

use Illuminate\Support\Arr;

if (!function_exists('path2plugin')) {
    /**
     * @param string|null $path
     * @return string
     */
    function path2plugin(?string $path = null): string
    {
        return path2platform('plugins' . DIRECTORY_SEPARATOR . $path);
    }
}

if (!function_exists('path2platform')) {
    /**
     * @param string|null $path
     * @return string
     */
    function path2platform(?string $path = null): string
    {
        return base_path('platform/' . $path);
    }
}

if (!function_exists('path2core')) {
    /**
     * @param string|null $path
     * @return string
     */
    function path2core(?string $path = null): string
    {
        return path2platform('core/' . $path);
    }
}

if (!function_exists('path2package')) {
    /**
     * @param string|null $path
     * @return string
     */
    function path2package(?string $path = null): string
    {
        return path2platform('packages/' . $path);
    }
}


if (! function_exists('getKeyValueFromAttributes')) {
    /**
     * Get the key value from the given attributes.
     *
     * @param object|array|string $attributes
     * @param string|null $key
     * @param string $explodeNeedle
     * @return array
     */
    function getKeyValueFromAttributes(object|array|string $attributes, ?string $key = null, string $explodeNeedle = ","): array
    {
        // Handle the case when a specific key is provided
        if (!empty($key)) {
            $attributes = is_array($attributes) ? ($attributes[$key] ?? []) : Arr::get($attributes, $key, []);
        }

        // Convert the attributes to an array of values
        if (is_array($attributes)) {
            $values = $attributes;
        } elseif (is_string($attributes)) {
            $values = json_validate($attributes) ? json_decode($attributes, true) : explode($explodeNeedle, $attributes);
        } else {
            $values = [];
        }

        // Filter out any empty values and return
        return array_filter($values);
    }

}