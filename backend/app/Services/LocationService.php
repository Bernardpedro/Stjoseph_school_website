<?php

namespace App\Services;

class LocationService
{
    public function all(): array
    {
        $path = APPPATH . 'Data/rwanda_locations.json';
        $json = is_file($path) ? file_get_contents($path) : false;
        $data = $json !== false ? json_decode($json, true) : null;

        return is_array($data) ? $data : [];
    }

    public function districts(?string $province): array
    {
        $needle = strtolower(trim((string) $province));
        if ($needle === '') {
            return [];
        }

        foreach ($this->all() as $item) {
            if (
                strtolower((string) ($item['code'] ?? '')) === $needle
                || strtolower((string) ($item['name'] ?? '')) === $needle
            ) {
                return array_values($item['districts'] ?? []);
            }
        }

        return [];
    }
}
