<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Arr;

/***
 * Get an item from the collection with multidimensional data using "dot" notation.
 *
 * @param mixed $key
 * @param mixed $default
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return mixed
 */
class Path
{
    public function __invoke()
    {
        return function ($key, $default = null) {
            return Arr::get($this->items, $key, $default);
        };
    }
}
