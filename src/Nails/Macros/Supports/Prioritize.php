<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Move elements to the start of the collection.
 *
 * @param  callable  $callable
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return \Illuminate\Support\Collection
 */
class Prioritize
{
    public function __invoke()
    {
        return function (callable $callable): Collection {
            $nonPrioritized = $this->reject($callable);

            return $this
                ->filter($callable)
                ->union($nonPrioritized);
        };
    }
}
