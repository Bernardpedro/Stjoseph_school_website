<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsAllowFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (strtolower($request->getMethod()) === 'options') {
            return service('response')->setStatusCode(204);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // CORS is set once in public/index.php. Do not send the same headers
        // again — Firefox treats duplicate Access-Control-Allow-Origin as a
        // network error with no response body.
        return $response;
    }
}
