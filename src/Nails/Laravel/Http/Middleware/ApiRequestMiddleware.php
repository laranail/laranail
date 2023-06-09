<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiRequestMiddleware extends ApiMiddleware
{
    /**
     * Handle the response.
     *
     * @param Request $request
     * @param Closure $next
     * @param string[] ...$options
     * @return mixed
     */
    public function handle($request, Closure $next, ...$options)
    {
        $request->replace($this->mutateKeys($request->all()));
        $request = $this->hook($request);

        return $next($request);
    }

    /**
     * Mutate the request keys before the payload is processed by the app.
     *
     * @param string $key
     * @return string|mixed
     */
    protected function mutateKey($key)
    {
        return $key;
    }

    /**
     * Hook into the request before forwarding.
     *
     * @param Request $request
     * @return Request
     */
    protected function hook(Request $request)
    {
        return $request;
    }
}
