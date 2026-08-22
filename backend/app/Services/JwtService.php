<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    protected string $secret;
    protected string $algorithm = 'HS256';
    protected int $accessTtl;

    public function __construct()
    {
        $this->secret = trim((string) env('JWT_SECRET'));

        $this->accessTtl = (int) env(
            'JWT_ACCESS_TTL',
            604800
        );

        if (empty($this->secret)) {
            throw new \RuntimeException(
                'JWT_SECRET is not configured.'
            );
        }
    }

    public function createAccessToken(string $userId, array $claims = []): string
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + $this->accessTtl;

        $payload = [
            'iss'  => base_url(),
            'sub'  => $userId,
            'role' => $claims['role'] ?? 'user',
            'iat'  => $issuedAt,
            'exp'  => $expiresAt,
        ];

        return JWT::encode(
            $payload,
            $this->secret,
            $this->algorithm
        );
    }

    public function verifyAccessToken(string $token): object
    {
        try {
            return JWT::decode(
                $token,
                new Key($this->secret, $this->algorithm)
            );
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'Invalid or expired access token.'
            );
        }
    }
}