<?php

namespace App\Services;

/**
 * Redis cache for public API reads. Falls back to the database if Redis is down.
 */
class ContentCache
{
    public const TTL_PUBLIC    = 300;
    public const TTL_SYNC      = 4;
    public const TTL_SEARCH    = 90;
    public const TTL_LOCATIONS = 86400;

    protected const PREFIX = 'sjcache_';

    protected static ?\Redis $client = null;
    protected static bool $failed = false;

    /**
     * @template T
     * @param callable(): T $producer
     * @return T
     */
    public static function remember(string $group, string $suffix, callable $producer, int $ttl = self::TTL_PUBLIC): mixed
    {
        $key = self::key($group, $suffix);
        $redis = self::client();
        if ($redis) {
            try {
                $raw = $redis->get($key);
                if (is_string($raw) && $raw !== '') {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }
                }
            } catch (\Throwable $e) {
                self::$failed = true;
                self::$client = null;
            }
        }

        $value = $producer();
        self::put($key, $value, $ttl);

        return $value;
    }

    public static function forget(string ...$groups): void
    {
        $redis = self::client();
        if (!$redis || $groups === []) {
            return;
        }

        try {
            foreach ($groups as $group) {
                $pattern = self::PREFIX . self::safe($group) . '_*';
                $iterator = null;
                do {
                    $keys = $redis->scan($iterator, $pattern, 100);
                    if (is_array($keys) && $keys !== []) {
                        $redis->del($keys);
                    }
                } while ((int) $iterator !== 0);
            }
        } catch (\Throwable $e) {
            self::$failed = true;
            self::$client = null;
        }
    }

    public static function ping(): bool
    {
        $redis = self::client();
        if (!$redis) {
            return false;
        }

        try {
            $reply = $redis->ping();
            return $reply === true || $reply === '+PONG' || $reply === 'PONG';
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function localeSuffix(string $extra = 'public'): string
    {
        return LocaleContext::nuxt() . '_' . $extra;
    }

    protected static function put(string $key, mixed $value, int $ttl): void
    {
        if ($value === null) {
            return;
        }

        $redis = self::client();
        if (!$redis) {
            return;
        }

        try {
            $redis->setex($key, max(1, $ttl), json_encode($value, JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            self::$failed = true;
            self::$client = null;
        }
    }

    protected static function key(string $group, string $suffix): string
    {
        return self::PREFIX . self::safe($group) . '_' . self::safe($suffix);
    }

    protected static function safe(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value) ?? $value;

        return trim($value, '_') ?: 'x';
    }

    protected static function client(): ?\Redis
    {
        if (self::$failed) {
            return null;
        }
        if (self::$client instanceof \Redis) {
            return self::$client;
        }
        if (!class_exists(\Redis::class)) {
            self::$failed = true;
            return null;
        }

        $cfg = config('Cache')->redis ?? [];

        try {
            $redis = new \Redis();
            $connected = $redis->connect(
                (string) ($cfg['host'] ?? '127.0.0.1'),
                (int) ($cfg['port'] ?? 6379),
                1.5
            );
            if (!$connected) {
                self::$failed = true;
                return null;
            }
            $password = $cfg['password'] ?? null;
            if (is_string($password) && $password !== '') {
                $redis->auth($password);
            }
            $redis->select((int) ($cfg['database'] ?? 0));
            self::$client = $redis;

            return self::$client;
        } catch (\Throwable $e) {
            self::$failed = true;
            return null;
        }
    }
}
