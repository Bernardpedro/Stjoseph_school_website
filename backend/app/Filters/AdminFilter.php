<?php

namespace App\Filters;

use App\Models\UserModel;
use App\Services\AuthContext;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtFilter = new JwtAuthFilter();
        $result = $jwtFilter->before($request);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $role = AuthContext::role();

        if (!$role && AuthContext::id()) {
            $user = (new UserModel())->find(AuthContext::id());
            $role = $user['role'] ?? null;
        }

        if ($role !== 'admin') {
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'success' => false,
                    'message' => \App\Services\I18n::line('Api.adminRequired'),
                ]);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
