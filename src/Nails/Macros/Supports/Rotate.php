<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Supports;

use Illuminate\Support\Collection;

/**
 * Rotate the items in the collection with given offset.
 *
 * @param int $offset
 *
 * @mixin \Illuminate\Support\Collection
 *
 * @return \Illuminate\Support\Collection
 * @todo revisit later for updates
 */
class Rotate
{
    public function __invoke()
    {
        return function (int $offset): Collection {
            if ($this->isEmpty()) {
                return new static();
            }

            $count = $this->count();

            $offset %= $count;

            if ($offset < 0) {
                $offset += $count;
            }

            return new static($this->slice($offset)->merge($this->take($offset)));
        };
    }
}
