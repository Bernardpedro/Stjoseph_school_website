<?php

namespace App\Services;

class I18n
{
    public const LOCALES = ['en', 'rw', 'sw', 'fr', 'de'];

    /**
     * CodeIgniter locale — API messages and validation.
     *
     * @param array<string, string> $args
     */
    public static function line(string $key, array $args = [], ?string $locale = null): string
    {
        $key = trim($key);
        if ($key === '') {
            return '';
        }

        if (!preg_match('/^[A-Z][A-Za-z]+\.[A-Za-z0-9_.]+$/', $key)) {
            return $key;
        }

        $locale = $locale ?: LocaleContext::codeigniter();
        $translated = lang($key, $args, $locale);
        if ($translated === $key && $locale !== 'en') {
            $translated = lang($key, $args, 'en');
        }

        return $translated;
    }

    /**
     * Nuxt locale — visitor-facing labels returned in API data.
     *
     * @param array<string, string> $args
     */
    public static function content(string $key, array $args = []): string
    {
        return self::line($key, $args, LocaleContext::nuxt());
    }

    public static function known(string $group, ?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        if (($group === 'program' || $group === 'level') && str_contains($value, ' | ')) {
            [$level, $program] = array_map('trim', explode(' | ', $value, 2));
            $parts = array_filter([
                $level !== '' ? self::known('level', $level) : '',
                $program !== '' ? self::known('program', $program) : '',
            ]);
            return $parts !== [] ? implode(' — ', $parts) : $value;
        }

        $slug = self::slug($group, $value);
        $line = self::content('Content.' . $group . '.' . $slug);

        return str_starts_with($line, 'Content.') ? $value : $line;
    }

    public static function stored(?string $text): string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        $match = StoredCatalog::resolve($text);
        if ($match === null) {
            return $text;
        }

        $line = self::content('Stored.' . $match['key'], $match['args']);

        return str_starts_with($line, 'Stored.') ? $text : $line;
    }

    /**
     * @param list<string> $fields
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    public static function localizeRow(array $row, array $fields): array
    {
        foreach ($fields as $field) {
            $row[$field . '_i18n'] = self::stored(isset($row[$field]) ? (string) $row[$field] : '');
        }

        return $row;
    }

    public static function slug(string $group, string $value): string
    {
        $normalized = trim($value);
        $maps = [
            'status' => [
                'pending'  => 'pending',
                'reviewed' => 'reviewed',
                'accepted' => 'accepted',
                'rejected' => 'rejected',
            ],
            'gender' => [
                'male'   => 'male',
                'female' => 'female',
                'm'      => 'male',
                'f'      => 'female',
            ],
            'level' => [
                'level 1 - short course' => 'level1',
                'level 1'                => 'level1',
                'l1'                     => 'level1',
                'level 3 - senior 4'     => 'level3',
                'level 3'                => 'level3',
                'l3'                     => 'level3',
                'level 4 - senior 5'     => 'level4',
                'level 4'                => 'level4',
                'l4'                     => 'level4',
                'level 5 - senior 6'     => 'level5',
                'level 5'                => 'level5',
                'l5'                     => 'level5',
            ],
            'program' => [
                'automobile technology' => 'auto',
                'building construction' => 'build',
                'tailoring'             => 'tailor',
                'carpentry'             => 'carp',
            ],
            'projectStatus' => [
                'ongoing'   => 'ongoing',
                'completed' => 'completed',
                'planned'   => 'planned',
            ],
            'userStatus' => [
                'active'   => 'active',
                'inactive' => 'inactive',
            ],
            'role' => [
                'admin' => 'admin',
                'user'  => 'user',
            ],
            'category' => [
                'events'        => 'events',
                'projects'      => 'projects',
                'announcements' => 'announcements',
                'academics'     => 'academics',
            ],
            'eventType' => [
                'academic'      => 'academic',
                'religious'     => 'religious',
                'conference'    => 'conference',
                'visit'         => 'visit',
                'tournament'    => 'tournament',
                'feast'         => 'feast',
                'entertainment' => 'entertainment',
                'other'         => 'other',
                'exhibition'    => 'exhibition',
            ],
        ];

        $lookup = strtolower($normalized);
        if (isset($maps[$group][$lookup])) {
            return $maps[$group][$lookup];
        }

        return preg_replace('/[^a-z0-9]+/', '', $lookup) ?: $lookup;
    }
}
