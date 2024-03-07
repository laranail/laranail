<?php

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
    function getKeyValueFromAttributes(object|array|string $attributes, ?string $key = null, $explodeNeedle = ",") : array
    {
        $attributes = empty($key) ? $attributes : Arr::get($attributes, $key, []);

        if (is_array($attributes)) {
            $values = $attributes;
        } elseif (is_string($attributes)) {
            if (json_validate($attributes)) {
                $values = json_decode($attributes, true);
            }else {
                $values = explode($explodeNeedle, $attributes);
            }
        } else {
            $values = [];
        }

        return array_filter($values);
    }
}