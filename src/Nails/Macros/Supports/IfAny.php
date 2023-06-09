<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Execute a callable if the collection isn't empty, then return the collection.
 *
 * @param callable callback
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return \Illuminate\Support\Collection
 */
class IfAny
{
    public function __invoke()
    {
        return function (callable $callback): Collection {
            if (! $this->isEmpty()) {
                $callback($this);
            }

            return $this;
        };
    }
}
