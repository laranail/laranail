<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Map a collection, then filter the results.
 *
 * @mixin \Illuminate\Support\Collection
 */
class FilterMap
{
    public function __invoke()
    {
        return function (callable $callback): Collection {
            return $this->map($callback)->filter();
        };
    }
}
