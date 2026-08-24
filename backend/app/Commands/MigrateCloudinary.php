<?php

namespace App\Commands;

use App\Services\UploadService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class MigrateCloudinary extends BaseCommand
{
    protected $group       = 'Cloudinary';
    protected $name        = 'cloudinary:migrate';
    protected $description = 'Upload existing local files to Cloudinary and update stored URLs.';
    protected $usage       = 'cloudinary:migrate';

    public function run(array $params)
    {
        $uploads = new UploadService();
        if (!$uploads->isCloudinaryReady()) {
            CLI::error('Cloudinary credentials are missing in .env');
            return;
        }

        $db = Database::connect();
        $moved = 0;
        $skipped = 0;

        $moved += $this->migrateJsonColumn($db, $uploads, 'events', 'images', static function ($images) {
            return is_array($images) ? $images : [];
        }, 'events');

        $moved += $this->migrateJsonColumn($db, $uploads, 'projects', 'media', static function ($media) {
            return is_array($media) ? $media : [];
        }, 'projects', 'url');
        $moved += $this->migrateJsonColumn($db, $uploads, 'projects', 'media', static function ($media) {
            return is_array($media) ? $media : [];
        }, 'projects', 'thumbnail');

        $moved += $this->migrateJsonColumn($db, $uploads, 'requirement_levels', 'urls', static function ($urls) {
            return is_array($urls) ? $urls : [];
        }, 'requirements', 'url');

        $moved += $this->migrateAdmissions($db, $uploads);

        CLI::write("Moved {$moved} local file(s) to Cloudinary. Skipped {$skipped} already-remote path(s).", 'green');
    }

    protected function migrateJsonColumn(
        $db,
        UploadService $uploads,
        string $table,
        string $column,
        callable $normalize,
        string $folder,
        ?string $urlKey = null
    ): int {
        $moved = 0;
        $rows = $db->table($table)->get()->getResultArray();

        foreach ($rows as $row) {
            $decoded = json_decode((string) ($row[$column] ?? '[]'), true);
            $items = $normalize($decoded);
            $changed = false;

            foreach ($items as $i => $item) {
                $current = $urlKey ? (is_array($item) ? ($item[$urlKey] ?? '') : '') : (is_string($item) ? $item : '');
                $next = $this->replaceLocal($uploads, (string) $current, $folder);
                if ($next === $current) {
                    continue;
                }

                if ($urlKey) {
                    if (!is_array($items[$i])) {
                        $items[$i] = ['url' => $next];
                    } else {
                        $items[$i][$urlKey] = $next;
                    }
                } else {
                    $items[$i] = $next;
                }
                $changed = true;
                $moved++;
            }

            if ($changed) {
                $db->table($table)->where('id', $row['id'])->update([
                    $column => json_encode($items),
                ]);
            }
        }

        return $moved;
    }

    protected function migrateAdmissions($db, UploadService $uploads): int
    {
        $moved = 0;
        $rows = $db->table('admissions')->get()->getResultArray();

        foreach ($rows as $row) {
            $docs = json_decode((string) ($row['documents'] ?? '{}'), true);
            if (!is_array($docs)) {
                continue;
            }

            $changed = false;
            foreach (['bulletin', 'other'] as $key) {
                $list = $docs[$key] ?? [];
                if (!is_array($list)) {
                    continue;
                }
                foreach ($list as $i => $path) {
                    $next = $this->replaceLocal($uploads, (string) $path, 'admissions');
                    if ($next !== $path) {
                        $docs[$key][$i] = $next;
                        $changed = true;
                        $moved++;
                    }
                }
            }

            if ($changed) {
                $db->table('admissions')->where('id', $row['id'])->update([
                    'documents' => json_encode($docs),
                ]);
            }
        }

        return $moved;
    }

    protected function replaceLocal(UploadService $uploads, string $path, string $folder): string
    {
        $path = trim($path);
        if ($path === '' || $uploads->isRemoteUrl($path)) {
            return $path;
        }

        $absolute = $uploads->localAbsolutePath($path);
        if (!$absolute) {
            return $path;
        }

        CLI::write("Uploading {$path}");
        return $uploads->uploadLocalPath($absolute, $folder);
    }
}
