<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;

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
        if (!empty($data['name']) && empty($data['firstName'])) {
            $parts = preg_split('/\s+/', trim((string) $data['name']), 2) ?: [];
            $data['firstName'] = $parts[0] ?? 'User';
            $data['lastName'] = $parts[1] ?? '';
        }

        if (empty($data['firstName']) || empty($data['email']) || empty($data['password'])) {
            throw new \RuntimeException('Name, email and password are required.');
        }

        unset($data['name'], $data['passwordConfirmation']);

        $data['role'] = $data['role'] ?? 'user';
        if (empty($data['roleId'])) {
            $data['roleId'] = Uuid::uuid4()->toString();
        }
        if (!isset($data['phone']) || $data['phone'] === '') {
            $data['phone'] = null;
        }

        return $this->userService->createUser($data);
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userService->getUserByEmail($email);

        if (!$user) {
            throw new \RuntimeException('Invalid email or password.');
        }

        if (!password_verify($password, $user['password'])) {
            throw new \RuntimeException('Invalid email or password.');
        }

        if (($user['status'] ?? 'active') === 'inactive') {
            throw new \RuntimeException('This account is inactive. Contact an administrator.');
        }

        $this->userService->touchLastLogin($user['id']);

        $role = $user['role'] ?? 'user';
        $accessToken = $this->jwtService->createAccessToken(
            $user['id'],
            ['role' => $role]
        );

        unset($user['password']);

        $name = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));

        return [
            'token'       => $accessToken,
            'accessToken' => $accessToken,
            'name'        => $name !== '' ? $name : ($user['email'] ?? 'User'),
            'role'        => $role,
            'id'          => $user['id'],
            'email'       => $user['email'] ?? '',
            'user'        => $user,
        ];
    }
}
