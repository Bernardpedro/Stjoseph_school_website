<?php

namespace App\Services;

class AuthService
{
    protected UserService $userService;
    protected JwtService $jwtService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->jwtService = new JwtService();
    }

    public function register(array $data): array
    {
        throw new \RuntimeException('Api.registrationClosed');
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userService->getUserByEmail($email);

        if (!$user) {
            throw new \RuntimeException('Api.invalidCredentials');
        }

        if (!password_verify($password, $user['password'])) {
            throw new \RuntimeException('Api.invalidCredentials');
        }

        if (($user['status'] ?? 'active') === 'inactive') {
            throw new \RuntimeException('Api.accountInactive');
        }

        $this->userService->touchLastLogin($user['id']);

        $presented = $this->userService->present($user);
        if ($this->userService->isOwner($user)) {
            $presented['role'] = UserService::ROLE_SUPER_ADMIN;
        }
        $role = $presented['role'] ?? 'user';
        $accessToken = $this->jwtService->createAccessToken(
            $user['id'],
            ['role' => $role]
        );

        return [
            'token'       => $accessToken,
            'accessToken' => $accessToken,
            'name'        => $presented['name'] ?? ($user['email'] ?? 'User'),
            'role'        => $role,
            'id'          => $user['id'],
            'email'       => $user['email'] ?? '',
            'user'        => $presented,
        ];
    }
}
