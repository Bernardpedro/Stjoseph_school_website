<?php

namespace App\Services;

use App\Models\SettingModel;

class SettingService
{
    protected SettingModel $model;

    public function __construct()
    {
        $this->model = new SettingModel();
    }

    public function getGroup(string $prefix): array
    {
        $rows = $this->model->like('setting_key', $prefix . '.', 'after')->findAll();
        $out = [];
        foreach ($rows as $row) {
            $key = substr($row['setting_key'], strlen($prefix) + 1);
            $out[$key] = $row['setting_value'];
        }
        return $out;
    }

    public function putGroup(string $prefix, array $values): array
    {
        foreach ($values as $key => $value) {
            $full = $prefix . '.' . $key;
            $existing = $this->model->where('setting_key', $full)->first();
            if ($existing) {
                $this->model->update($existing['id'], ['setting_value' => (string) $value]);
            } else {
                $this->model->insert([
                    'setting_key'   => $full,
                    'setting_value' => (string) $value,
                ]);
            }
        }

        return $this->getGroup($prefix);
    }
}
