<?php

namespace App\Filters;

use App\Models\UserModel;
use App\Services\AuthContext;
use App\Services\UserService;
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

        $user = null;
        $role = AuthContext::role();

        if (AuthContext::id()) {
            $user = (new UserModel())->find(AuthContext::id());
            if (!$role) {
                $role = $user['role'] ?? null;
            }
        }

        if (UserService::isStaffRole($role) || (new UserService())->isOwner($user ?: null)) {
            return null;
        }

        return service('response')
            ->setStatusCode(403)
            ->setJSON([
                'success' => false,
                'message' => \App\Services\I18n::line('Api.adminRequired'),
            ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
