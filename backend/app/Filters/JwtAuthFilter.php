<?php

namespace App\Filters;

use App\Services\AuthContext;
use App\Services\JwtService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JwtAuthFilter implements FilterInterface
{
    public function before(
        RequestInterface $request,
        $arguments = null
    ) {
        AuthContext::set(null);

        $header = $request->getHeaderLine('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Authorization token is required.',
                ]);
        }

        $token = substr($header, 7);

        try {
            $jwtService = new JwtService();
            AuthContext::set($jwtService->verifyAccessToken($token));
        } catch (\Throwable $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid or expired access token.',
                ]);
        }

        return null;
    }

    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ) {
    }
}
