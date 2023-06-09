<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Execute a callable if the collection is empty, then return the collection.
 *
 * @mixin \Illuminate\Support\Collection
 */
class IfEmpty
{
    public function __invoke()
    {
        return function (callable $callback): Collection {
            if ($this->isEmpty()) {
                $callback($this);
            }

            return $this;
        };
    }
}
