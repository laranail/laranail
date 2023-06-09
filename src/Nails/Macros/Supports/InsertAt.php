<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Inserts an item at a given index with an optional key.
 *
 * @param  int   $index
 * @param  mixed $item
 * @param  mixed $key
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return Collection
 * @todo revisit later for updates
 */
class InsertAt
{
    public function __invoke()
    {
        return function (int $index, $item, $key = null): Collection {
            $after = $this->splice($index);
            $this->items = isset($key)
                    ? $this->put($key, $item)->merge($after)->toArray()
                    : $this->push($item)->merge($after)->toArray();

            return $this;
        };
    }
}
