<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Macros\Helpers\Collection;

use Closure;
use Illuminate\Support\Enumerable;
use ReflectionException;
use ReflectionFunction;
use Throwable;

/**
 * https://freek.dev/1691-adding-trycatch-to-laravel-collections
 *
 * @mixin Enumerable
 */
class CatchableProxy
{
    protected Enumerable $collection;

    protected array $calledMethods = [];

    public function __construct(Enumerable $collection)
    {
        $this->collection = $collection;
    }

    public function __call(string $method, array $parameters): self
    {
        $this->calledMethods[] = ['name' => $method, 'parameters' => $parameters];

        return $this;
    }

    /**
     * @throws ReflectionException
     * @throws Throwable
     */
    public function catch(Closure ...$handlers): Enumerable
    {
        $originalCollection = $this->collection;

        try {
            foreach ($this->calledMethods as $calledMethod) {
                $this->collection = $this->collection->{$calledMethod['name']}(...$calledMethod['parameters']);
            }
        } catch (Throwable $exception) {
            foreach ($handlers as $callable) {
                $type = $this->exceptionType($callable);
                if ($exception instanceof $type) {
                    return $callable($exception, $originalCollection) ?? $originalCollection;
                }
            }

            throw $exception;
        }

        return $this->collection;
    }

    /**
     * @throws ReflectionException
     */
    private function exceptionType(Closure $callable): string
    {
        $reflection = new ReflectionFunction($callable);

        if (empty($reflection->getParameters())) {
            return Throwable::class;
        }

        return optional($reflection->getParameters()[0]->getType())->getName() ?? Throwable::class;
    }
}