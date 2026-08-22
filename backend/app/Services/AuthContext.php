<?php

namespace App\Services;

class AuthContext
{
    private static ?object $user = null;

    public static function set(?object $user): void
    {
        self::$user = $user;
    }

    public static function get(): ?object
    {
        return self::$user;
    }

    public static function id(): ?string
    {
        if (self::$user === null || empty(self::$user->sub)) {
            return null;
        }

        return (string) self::$user->sub;
    }

    public static function role(): ?string
    {
        if (self::$user === null || !isset(self::$user->role) || self::$user->role === '') {
            return null;
        }

        return (string) self::$user->role;
    }
}
