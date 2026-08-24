<?php

namespace App\Services;

class LocaleContext
{
    protected static string $codeigniter = 'en';
    protected static string $nuxt = 'en';

    public static function set(string $codeigniter, string $nuxt): void
    {
        self::$codeigniter = self::normalize($codeigniter);
        self::$nuxt = self::normalize($nuxt);
    }

    public static function codeigniter(): string
    {
        return self::$codeigniter;
    }

    public static function nuxt(): string
    {
        return self::$nuxt;
    }

    /**
     * @return array{codeigniter: string, nuxt: string}
     */
    public static function both(): array
    {
        return [
            'codeigniter' => self::$codeigniter,
            'nuxt'        => self::$nuxt,
        ];
    }

    public static function normalize(?string $value): string
    {
        $supported = config('App')->supportedLocales ?: I18n::LOCALES;
        $fallback = config('App')->defaultLocale ?: ($supported[0] ?? 'en');
        $value = strtolower(str_replace('_', '-', trim((string) $value)));
        if ($value === '') {
            return $fallback;
        }

        $short = explode('-', $value)[0];
        if (in_array($short, $supported, true)) {
            return $short;
        }
        if (in_array($value, $supported, true)) {
            return $value;
        }

        return $fallback;
    }
}
