<?php

namespace App\Services;

/**
 * Short, non-sequential admission registration numbers.
 * Format: STJ-2026-K7M4Q9 (15 characters).
 */
class RegistrationNumberService
{
    public const PREFIX = 'STJ';

    /** Ambiguous 0/O/1/I/L omitted so applicants can type the code more easily. */
    private const ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    private const RANDOM_LENGTH = 6;

    public static function generate(?int $year = null): string
    {
        $year ??= (int) date('Y');
        $max = strlen(self::ALPHABET) - 1;
        $token = '';
        for ($i = 0; $i < self::RANDOM_LENGTH; $i++) {
            $token .= self::ALPHABET[random_int(0, $max)];
        }

        return sprintf('%s-%d-%s', self::PREFIX, $year, $token);
    }

    public static function normalize(?string $value): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim((string) $value)) ?? '');
    }
}
