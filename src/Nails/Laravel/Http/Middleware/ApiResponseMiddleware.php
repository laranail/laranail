<?php declare(strict_types=1);

namespace Simtabi\Laranail\Nails\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Simtabi\Laranail\Nails\Laravel\Contracts\ShovelHttpInterface;

class ApiResponseMiddleware extends ApiMiddleware implements ShovelHttpInterface
{
    /**
     * Acceptable response classes that will be handled.
     *
     * @var array
     */
    private $acceptedResponses = [
        Response::class,
        \Illuminate\Http\JsonResponse::class,
        \Illuminate\Routing\ResponseFactory::class,
    ];

    /**
     * Handle the response.
     *
     * @param Request $request
     * @param \Closure  $next
     * @param string[] ...$options
     * @return Response
     */
    public function handle($request, Closure $next, ...$options)
    {
        $response = $next($request);

        if ($this->shouldBuild($request, $response)) {
            $response = $this->hook($request, $response);
            $response = $this->buildPayload($response, ...$options);
        }

        return $response;
    }

    /**
     * Construct the response payload.
     *
     * @param Response $response
     * @param string[] ...$options
     * @return Response
     */
    private function buildPayload($response, ...$options)
    {
        $metaTag = $options[0] ?? 'meta';
        $dataTag = $options[1] ?? 'data';
        $pageTag = $options[2] ?? 'pagination';

        $payload = $this->getMetaBlock($response, $metaTag);

        if ($response->content()) {
            if ($this->isPaginated($response)) {
                $payload[$metaTag][$pageTag] = $this->getPaginationBlock($response->original);
                $payload[$dataTag] = $response->original->items();
            } elseif ($this->isPaginatedCollection($response)) {
                $payload[$metaTag][$pageTag] = $this->getPaginationBlock($response->original->resource);
                $payload[$dataTag] = $response->original->resource->items();
            } else {
                $payload[$dataTag] = json_decode($response->content(), true);
            }
        }

        if (isset($payload[$dataTag])) {
            $payload[$dataTag] = $this->mutateKeys($payload[$dataTag]);
        }

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->setContent(json_encode($payload));
        } else {
            $response->setContent($payload);
        }

        return $response;
    }

    /**
     * Returns a string defining whether or not the response is successful.
     *
     * @param int $code
     * @return string
     */
    private function getStatus(int $code)
    {
        $range = substr($code, 0, 1);

        if (in_array($range, [4, 5])) {
            return 'error';
        }

        return 'success';
    }

    /**
     * Returns the text representation of the HTTP status code.
     *
     * @param int $code
     * @return string
     */
    private function getStatusMessage(int $code)
    {
        return self::CODES[$code] ?? 'Unknown';
    }

    /**
     * Returns true if the response is a paginated object.
     *
     * @param Response $response
     * @return bool
     */
    private function isPaginated($response)
    {
        return $response->original instanceof LengthAwarePaginator;
    }

    /**
     * Returns true if the response is a paginated collection.
     *
     * @param Response $response
     * @return bool
     */
    private function isPaginatedCollection($response)
    {
        return isset($response->original->resource) &&
               $response->original->resource instanceof LengthAwarePaginator;
    }

    /**
     * Constructs and returns the meta object.
     *
     * @param Response $response
     * @param string $metaTag
     * @return array
     */
    private function getMetaBlock($response, $metaTag)
    {
        $payload = [
            $metaTag => [
               'code'    => $response->status(),
               'status'  => $this->getStatus($response->status()),
               'message' => $this->getStatusMessage($response->status()),
             ]
         ];

        if (isset($response->additionalMeta)) {
            $payload[$metaTag] = array_merge($payload[$metaTag], $response->additionalMeta);
        }

        return $payload;
    }

    /**
     * Constructs and returns the pagination object.
     *
     * @param Response $response
     * @return array
     */
    private function getPaginationBlock($paginator)
    {
        return [
            'records'  => $paginator->total(),
            'page'     => $paginator->currentPage(),
            'pages'    => $paginator->lastPage(),
            'limit'    => intval($paginator->perPage()),
        ];
    }

    /**
     * Determine if the response should be built.
     *
     * @param Response $response
     * @param Request $request
     * @return bool
     */
    private function shouldBuild($request, $response)
    {
        return in_array(get_class($response), $this->acceptedResponses);
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
     * Hook into the response before forwarding.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    protected function hook($request, $response)
    {
        return $response;
    }
}
