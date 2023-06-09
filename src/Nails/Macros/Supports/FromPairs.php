<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Transform a collection into an associative array form collection item.
 *
 * @mixin \Illuminate\Support\Collection
 */
class FromPairs
{
    public function __invoke()
    {
        return function (): Collection {
            return $this->reduce(function ($assoc, array $keyValuePair): Collection {
                [$key, $value] = $keyValuePair;
                $assoc[$key] = $value;

                return $assoc;
            }, new static());
        };
    }
}
