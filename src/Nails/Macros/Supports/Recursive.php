<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Recursively convert arrays and objects within a multidimensional array to Collections
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return \Illuminate\Support\Collection
 */
class Recursive
{
    public function __invoke()
    {
        return function (): Collection
        {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        };
    }
}
