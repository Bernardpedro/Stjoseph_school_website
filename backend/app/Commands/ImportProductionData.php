<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Ramsey\Uuid\Uuid;

class ImportProductionData extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:import-production';
    protected $description = 'Import the old production dump into the current school tables.';
    protected $usage       = 'db:import-production [sqlPath]';

    public function run(array $params)
    {
        $path = $params[0] ?? 'C:\\Users\\COCOCE LTD\\Downloads\\nzuki_db (2).sql';
        if (!is_file($path)) {
            CLI::error('SQL file not found: ' . $path);
            return;
        }

        $sourceName = 'nzuki_import';
        $this->loadDump($path, $sourceName);

        $source = Database::connect($this->sourceGroup($sourceName));
        $target = Database::connect();

        $counts = [
            'announcements' => $this->importAnnouncements($source, $target),
            'admission_items' => $this->importAdmissionItems($source, $target),
            'admissions' => $this->importAdmissions($source, $target),
            'events' => $this->importEvents($source, $target),
            'requirement_levels' => $this->importRequirementLevels($source, $target),
            'settings' => $this->importSettings($source, $target),
            'notifications' => $this->importNotifications($source, $target),
            'users' => $this->importUsers($source, $target),
        ];

        $source->close();
        Database::forge()->dropDatabase($sourceName);

        foreach ($counts as $label => $count) {
            CLI::write("Imported {$count} {$label}", 'green');
        }
        CLI::write('Production projects table was empty, so existing project records were kept.', 'yellow');
        CLI::write('Event/requirement/admission files still point at old local uploads/ paths. Re-upload those files in admin if the pictures are missing.', 'yellow');
    }

    protected function loadDump(string $path, string $database): void
    {
        $admin = Database::connect();
        $admin->query('DROP DATABASE IF EXISTS `' . $database . '`');
        $admin->query('CREATE DATABASE `' . $database . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');

        $cfg = config('Database')->default;
        $mysqli = new \mysqli(
            $cfg['hostname'] ?: 'localhost',
            $cfg['username'] ?: 'root',
            (string) $cfg['password'],
            $database,
            (int) ($cfg['port'] ?: 3306)
        );
        if ($mysqli->connect_error) {
            throw new \RuntimeException('Could not connect to import database.');
        }
        $mysqli->set_charset('utf8mb4');

        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new \RuntimeException('Could not read the SQL dump.');
        }

        if (!$mysqli->multi_query($sql)) {
            throw new \RuntimeException('Could not import SQL dump: ' . $mysqli->error);
        }
        while ($mysqli->more_results()) {
            $mysqli->next_result();
        }
        $mysqli->close();
    }

    protected function sourceGroup(string $database): array
    {
        $group = config('Database')->default;
        $group['database'] = $database;
        return $group;
    }

    protected function importAnnouncements($source, $target): int
    {
        $rows = $source->table('announcements')->orderBy('sort_order', 'ASC')->get()->getResultArray();
        $target->table('announcements')->truncate();
        foreach ($rows as $row) {
            $target->table('announcements')->insert([
                'id'         => $row['id'],
                'title'      => $row['title'],
                'message'    => $row['message'],
                'cta_text'   => $row['cta_text'] ?: 'Apply Now',
                'link'       => $row['link'] ?: '/admission',
                'sort_order' => (int) $row['sort_order'],
                'is_active'  => (int) $row['is_active'],
                'created_at' => $this->dt($row['created_at'] ?? null),
                'updated_at' => $this->dt($row['updated_at'] ?? null),
            ]);
        }
        return count($rows);
    }

    protected function importAdmissionItems($source, $target): int
    {
        $rows = $source->table('admission_requirements')->orderBy('sort_order', 'ASC')->get()->getResultArray();
        $target->table('admission_requirement_items')->truncate();
        foreach ($rows as $row) {
            $target->table('admission_requirement_items')->insert([
                'item_text'  => $row['item_text'],
                'sort_order' => (int) $row['sort_order'],
                'is_active'  => (int) $row['is_active'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return count($rows);
    }

    protected function importAdmissions($source, $target): int
    {
        $rows = $source->table('admission_applications')->orderBy('id', 'ASC')->get()->getResultArray();
        $target->table('admissions')->truncate();
        foreach ($rows as $row) {
            $program = (string) ($row['program'] ?? '');
            $target->table('admissions')->insert([
                'id'              => $row['id'],
                'student_name'    => $row['student_name'],
                'date_of_birth'   => $row['date_of_birth'] ?: null,
                'gender'          => $row['gender'] ?: null,
                'level'           => $this->guessLevel($program),
                'program'         => $program ?: null,
                'previous_school' => $row['previous_school'] ?: null,
                'parent_name'     => $row['parent_name'],
                'parent_phone'    => $row['parent_phone'],
                'parent_email'    => $row['parent_email'] ?: null,
                'province'        => null,
                'district'        => null,
                'address'         => $row['address'] ?: null,
                'message'         => $row['message'] ?: null,
                'documents'       => $this->normalizeDocuments($row['documents'] ?? null),
                'status'          => $row['status'] ?: 'pending',
                'created_at'      => $this->dt($row['created_at'] ?? null),
                'updated_at'      => $this->dt($row['updated_at'] ?? null),
            ]);
        }
        return count($rows);
    }

    protected function importEvents($source, $target): int
    {
        $rows = $source->table('events')->orderBy('id', 'ASC')->get()->getResultArray();
        $target->table('events')->truncate();
        foreach ($rows as $row) {
            $target->table('events')->insert([
                'id'          => $row['id'],
                'title'       => $this->fixText($row['title'] ?? ''),
                'description' => $this->fixText($row['description'] ?? ''),
                'date'        => $row['date'] ?? '',
                'time'        => $row['time'] ?? '',
                'location'    => $this->fixText($row['location'] ?? ''),
                'type'        => $row['type'] ?? '',
                'status'      => $row['status'] ?? '',
                'organizer'   => $this->fixText($row['organizer'] ?? ''),
                'youtubeLink' => $row['youtubeLink'] ?? '',
                'images'      => $this->normalizeJsonList($row['images'] ?? null),
                'created_at'  => $this->dt($row['created_at'] ?? null),
                'updated_at'  => $this->dt($row['updated_at'] ?? null),
            ]);
        }
        return count($rows);
    }

    protected function importRequirementLevels($source, $target): int
    {
        $rows = $source->table('requirement_levels')->orderBy('sort_order', 'ASC')->get()->getResultArray();
        $urlsByLevel = [];
        if ($source->tableExists('requirement_urls')) {
            foreach ($source->table('requirement_urls')->get()->getResultArray() as $urlRow) {
                $id = (int) ($urlRow['requirement_id'] ?? 0);
                $url = trim((string) ($urlRow['url'] ?? ''));
                if ($id && $url !== '') {
                    $urlsByLevel[$id][] = ['url' => str_replace('\\', '/', $url)];
                }
            }
        }

        $target->table('requirement_levels')->truncate();
        foreach ($rows as $row) {
            $target->table('requirement_levels')->insert([
                'id'          => $row['id'],
                'code'        => $row['code'] ?: 'level',
                'name'        => $row['name'] ?: 'Level',
                'description' => $row['description'] ?: '',
                'sort_order'  => (int) $row['sort_order'],
                'urls'        => json_encode($urlsByLevel[(int) $row['id']] ?? []),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }
        return count($rows);
    }

    protected function importSettings($source, $target): int
    {
        $count = 0;
        if ($source->tableExists('requirement_settings')) {
            $req = $source->table('requirement_settings')->get()->getRowArray();
            if ($req) {
                $this->upsertSetting($target, 'requirements.title', $req['title'] ?? '');
                $this->upsertSetting($target, 'requirements.subtitle', $req['subtitle'] ?? '');
                $this->upsertSetting($target, 'requirements.academic_year', $req['academic_year'] ?? '');
                $count += 3;
            }
        }
        if ($source->tableExists('partner_settings')) {
            $partner = $source->table('partner_settings')->get()->getRowArray();
            if ($partner) {
                $this->upsertSetting($target, 'projects.title', $partner['title'] ?? '');
                $this->upsertSetting($target, 'projects.description', $partner['description'] ?? '');
                $count += 2;
            }
        }
        return $count;
    }

    protected function importNotifications($source, $target): int
    {
        $rows = $source->table('notifications')->orderBy('id', 'ASC')->get()->getResultArray();
        $target->table('notifications')->truncate();
        foreach ($rows as $row) {
            $target->table('notifications')->insert([
                'id'         => $row['id'],
                'title'      => $row['title'],
                'message'    => $row['message'],
                'link'       => $row['link'] ?: null,
                'is_read'    => (int) $row['is_read'],
                'created_at' => $this->dt($row['created_at'] ?? null),
                'updated_at' => $this->dt($row['created_at'] ?? null),
            ]);
        }
        return count($rows);
    }

    protected function importUsers($source, $target): int
    {
        if (!$source->tableExists('users')) {
            return 0;
        }

        $count = 0;
        foreach ($source->table('users')->get()->getResultArray() as $row) {
            $email = trim((string) ($row['email'] ?? ''));
            if ($email === '') {
                continue;
            }
            $existing = $target->table('users')->where('email', $email)->get()->getRowArray();
            if ($existing) {
                $target->table('users')->where('email', $email)->update([
                    'role'     => ($row['role'] ?? '') === 'admin' ? 'admin' : 'user',
                    'status'   => 'active',
                    'password' => $row['password'],
                ]);
                $count++;
                continue;
            }

            $name = trim((string) ($row['name'] ?? 'Admin'));
            $parts = preg_split('/\s+/', $name, 2) ?: [];
            $target->table('users')->insert([
                'id'         => Uuid::uuid4()->toString(),
                'firstName'  => $parts[0] ?: 'Admin',
                'lastName'   => $parts[1] ?? '',
                'email'      => $email,
                'phone'      => null,
                'roleId'     => Uuid::uuid4()->toString(),
                'password'   => $row['password'],
                'role'       => ($row['role'] ?? '') === 'admin' ? 'admin' : 'user',
                'status'     => 'active',
                'created_at' => $this->dt($row['created_at'] ?? null),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }

        return $count;
    }

    protected function upsertSetting($target, string $key, string $value): void
    {
        $existing = $target->table('settings')->where('setting_key', $key)->get()->getRowArray();
        $now = date('Y-m-d H:i:s');
        if ($existing) {
            $target->table('settings')->where('setting_key', $key)->update([
                'setting_value' => $value,
                'updated_at'    => $now,
            ]);
            return;
        }
        $target->table('settings')->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }

    protected function guessLevel(string $program): ?string
    {
        if (preg_match('/level\s*([0-9]+)/i', $program, $m)) {
            return 'Level ' . $m[1];
        }
        return $program !== '' ? $program : null;
    }

    protected function normalizeDocuments(?string $json): string
    {
        $data = json_decode((string) $json, true);
        if (!is_array($data)) {
            $data = ['bulletin' => [], 'other' => []];
        }
        foreach (['bulletin', 'other'] as $key) {
            $data[$key] = array_values(array_map(
                static fn ($path) => str_replace('\\', '/', (string) $path),
                $data[$key] ?? []
            ));
        }
        return json_encode($data);
    }

    protected function normalizeJsonList(?string $json): string
    {
        $data = json_decode((string) $json, true);
        if (!is_array($data)) {
            return json_encode([]);
        }
        $out = [];
        foreach ($data as $item) {
            if (is_string($item) && trim($item) !== '') {
                $out[] = str_replace('\\', '/', $item);
            }
        }
        return json_encode($out);
    }

    protected function fixText(?string $text): string
    {
        $text = (string) $text;
        $text = str_replace(['Ã©', 'â€™', '\\\\r\\\\n', '\\r\\n'], ['é', "'", "\n", "\n"], $text);
        return $text;
    }

    protected function dt(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        $ts = strtotime($value);
        return $ts ? date('Y-m-d H:i:s', $ts) : $value;
    }
}
