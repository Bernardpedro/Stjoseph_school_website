<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Models\UserModel;

class UserService
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function createUser(array $data): array
    {
        $data = $this->normalize($data);

        if (empty($data['email']) || empty($data['password'])) {
            throw new \RuntimeException('Api.emailPasswordRequired');
        }

        $existingUser = $this->userModel
            ->where('email', $data['email'])
            ->first();

        if ($existingUser) {
            throw new \RuntimeException('Api.emailTaken');
        }

        $data['role'] = $this->normalizeRole($data['role'] ?? 'user');
        $data['status'] = $this->normalizeStatus($data['status'] ?? 'active');
        $data['firstName'] = $data['firstName'] ?? 'User';
        $data['lastName'] = $data['lastName'] ?? '';
        if (empty($data['roleId'])) {
            $data['roleId'] = Uuid::uuid4()->toString();
        }

        $data['id'] = Uuid::uuid4()->toString();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->userModel->insert($data);

        $user = $this->userModel->find($data['id']);
        return $this->present($user ?: $data);
    }

    public function getUsers(): array
    {
        $users = $this->userModel
            ->select('id, firstName, lastName, email, phone, role, status, last_login, created_at, updated_at')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return array_map([$this, 'present'], $users);
    }

    public function getUserById(string $id): ?array
    {
        $user = $this->userModel
            ->select('id, firstName, lastName, email, phone, role, status, last_login, created_at, updated_at')
            ->find($id);

        return $user ? $this->present($user) : null;
    }

    public function getUserByEmail(string $email): ?array
    {
        return $this->userModel
            ->where('email', $email)
            ->first();
    }

    public function deleteUser(string $id, ?string $actorId = null): bool
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return false;
        }

        if ($actorId && $actorId === $id) {
            throw new \RuntimeException('Api.cannotDeleteSelf');
        }

        if (($user['role'] ?? '') === 'admin') {
            $adminCount = $this->userModel->where('role', 'admin')->countAllResults();
            if ($adminCount <= 1) {
                throw new \RuntimeException('Api.cannotDeleteLastAdmin');
            }
        }

        return (bool) $this->userModel->delete($id);
    }

    public function updateUser(string $id, array $data, ?string $actorId = null): ?array
    {
        $data = $this->normalize($data);
        $user = $this->userModel->find($id);

        if (!$user) {
            return null;
        }

        if (isset($data['password'])) {
            if ($data['password'] === '') {
                unset($data['password']);
            } else {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
        }

        if (isset($data['role'])) {
            $data['role'] = $this->normalizeRole($data['role']);
            if ($data['role'] !== 'admin' && ($user['role'] ?? '') === 'admin') {
                $adminCount = $this->userModel->where('role', 'admin')->countAllResults();
                if ($adminCount <= 1) {
                    throw new \RuntimeException('Api.cannotDemoteLastAdmin');
                }
            }
        }

        if (isset($data['status'])) {
            $data['status'] = $this->normalizeStatus($data['status']);
            if ($data['status'] === 'inactive' && ($user['role'] ?? '') === 'admin' && $actorId === $id) {
                throw new \RuntimeException('Api.cannotDeactivateSelf');
            }
        }

        unset($data['id'], $data['name'], $data['roleId']);

        if ($data !== []) {
            $this->userModel->update($id, $data);
        }

        return $this->getUserById($id);
    }

    public function touchLastLogin(string $id): void
    {
        $this->userModel->update($id, [
            'last_login' => date('Y-m-d H:i:s'),
        ]);
    }

    public function present(?array $user): array
    {
        if (!$user) {
            return [];
        }

        unset($user['password']);
        $name = trim(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));
        $user['name'] = $name !== '' ? $name : ($user['email'] ?? 'User');
        $user['role'] = $this->normalizeRole($user['role'] ?? 'user');
        $user['status'] = $this->normalizeStatus($user['status'] ?? 'active');
        $user['role_label'] = \App\Services\I18n::known('role', $user['role']);
        $user['status_label'] = \App\Services\I18n::known('userStatus', $user['status']);
        return $user;
    }

    protected function normalize(array $data): array
    {
        if (!empty($data['name']) && empty($data['firstName'])) {
            $parts = preg_split('/\s+/', trim((string) $data['name']), 2) ?: [];
            $data['firstName'] = $parts[0] ?? 'User';
            $data['lastName'] = $parts[1] ?? '';
        }

        if (isset($data['phone']) && $data['phone'] === '') {
            $data['phone'] = null;
        }

        if (isset($data['role'])) {
            $data['role'] = $this->normalizeRole($data['role']);
        }

        if (isset($data['status'])) {
            $data['status'] = $this->normalizeStatus($data['status']);
        }

        return $data;
    }

    protected function normalizeRole(mixed $role): string
    {
        $role = strtolower(trim((string) $role));
        return $role === 'admin' ? 'admin' : 'user';
    }

    protected function normalizeStatus(mixed $status): string
    {
        $status = strtolower(trim((string) $status));
        if (in_array($status, ['inactive', 'disabled', 'banned'], true)) {
            return 'inactive';
        }
        return 'active';
    }
}
