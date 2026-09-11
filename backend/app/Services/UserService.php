<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use App\Models\UserModel;

class UserService
{
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Email of the school-owner account. Configured via SUPER_ADMIN_EMAIL so
     * it is never hardcoded per school. Returns '' when unset, which safely
     * fails email-based ownership checks (role-based checks still work).
     */
    protected static function superAdminEmail(): string
    {
        return trim((string) env('SUPER_ADMIN_EMAIL'));
    }

    public static function isStaffRole(?string $role): bool
    {
        return in_array((string) $role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN], true);
    }

    public function isOwner(?array $user): bool
    {
        if (!$user) {
            return false;
        }

        if (($user['role'] ?? '') === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        $ownerEmail = self::superAdminEmail();

        return $ownerEmail !== '' && strcasecmp((string) ($user['email'] ?? ''), $ownerEmail) === 0;
    }

    public function actorIsOwner(?string $actorId): bool
    {
        if (!$actorId) {
            return false;
        }

        $actor = $this->userModel->find($actorId);

        return $this->isOwner($actor ?: null);
    }

    public function upsertOwner(): array
    {
        $email = self::superAdminEmail();
        if ($email === '') {
            throw new \RuntimeException('SUPER_ADMIN_EMAIL is not configured.');
        }

        $password = trim((string) env('SUPER_ADMIN_PASSWORD'));
        if ($password === '') {
            throw new \RuntimeException('SUPER_ADMIN_PASSWORD is not configured.');
        }

        $existing = $this->getUserByEmail($email);
        $payload = [
            'firstName' => 'School',
            'lastName'  => 'Administrator',
            'email'     => $email,
            'password'  => $password,
            'role'      => self::ROLE_SUPER_ADMIN,
            'status'    => 'active',
        ];

        if ($existing) {
            return $this->updateUser($existing['id'], $payload, null, true) ?? $this->present($existing);
        }

        return $this->createUser($payload, true);
    }

    public function createUser(array $data, bool $allowOwnerRole = false): array
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
        if ($data['role'] === self::ROLE_SUPER_ADMIN && !$allowOwnerRole) {
            throw new \RuntimeException('Api.cannotAssignOwnerRole');
        }

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

        if ($this->isOwner($user)) {
            throw new \RuntimeException('Api.cannotDeleteOwner');
        }

        if (self::isStaffRole($user['role'] ?? '')) {
            if ($this->countStaffAdmins() <= 1) {
                throw new \RuntimeException('Api.cannotDeleteLastAdmin');
            }
        }

        return (bool) $this->userModel->delete($id);
    }

    public function updateUser(string $id, array $data, ?string $actorId = null, bool $privileged = false): ?array
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

        if (!$privileged && $this->isOwner($user)) {
            if (isset($data['role']) && $data['role'] !== self::ROLE_SUPER_ADMIN) {
                throw new \RuntimeException('Api.cannotDemoteOwner');
            }
            if (isset($data['status']) && $data['status'] === 'inactive') {
                throw new \RuntimeException('Api.cannotDeactivateOwner');
            }
            if (isset($data['email']) && strcasecmp((string) $data['email'], self::superAdminEmail()) !== 0) {
                throw new \RuntimeException('Api.cannotModifyOwner');
            }
        }

        if (isset($data['role'])) {
            $data['role'] = $this->normalizeRole($data['role']);
            if ($data['role'] === self::ROLE_SUPER_ADMIN && !$privileged && !$this->isOwner($user)) {
                throw new \RuntimeException('Api.cannotAssignOwnerRole');
            }
            if (
                !self::isStaffRole($data['role'])
                && self::isStaffRole($user['role'] ?? '')
                && $this->countStaffAdmins() <= 1
            ) {
                throw new \RuntimeException('Api.cannotDemoteLastAdmin');
            }
        }

        if (isset($data['status'])) {
            $data['status'] = $this->normalizeStatus($data['status']);
            if ($data['status'] === 'inactive' && self::isStaffRole($user['role'] ?? '') && $actorId === $id) {
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

    protected function countStaffAdmins(): int
    {
        return $this->userModel
            ->whereIn('role', [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN])
            ->countAllResults();
    }

    protected function normalizeRole(mixed $role): string
    {
        $role = strtolower(trim(str_replace(['-', ' '], '_', (string) $role)));
        if (in_array($role, [self::ROLE_SUPER_ADMIN, 'superadmin', 'owner'], true)) {
            return self::ROLE_SUPER_ADMIN;
        }

        return $role === self::ROLE_ADMIN ? self::ROLE_ADMIN : self::ROLE_USER;
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
