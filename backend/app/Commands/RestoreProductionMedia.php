<?php

namespace App\Commands;

use App\Services\UploadService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class RestoreProductionMedia extends BaseCommand
{
    protected $group       = 'Cloudinary';
    protected $name        = 'cloudinary:restore-media';
    protected $description = 'Resolve old local upload paths to Cloudinary or production files.';
    protected $usage       = 'cloudinary:restore-media';

    protected array $origins = [
        'https://api.stjosephtssnzuki.com/',
        'https://stjosephtssnzuki.com/',
        'https://www.stjosephtssnzuki.com/',
    ];

    public function run(array $params)
    {
        $uploads = new UploadService();
        $db = Database::connect();
        $moved = 0;
        $missing = 0;

        $moved += $this->rewriteJson($db, $uploads, 'events', 'images', null, 'events', $missing);
        $moved += $this->rewriteJson($db, $uploads, 'projects', 'media', 'url', 'projects', $missing);
        $moved += $this->rewriteJson($db, $uploads, 'projects', 'media', 'thumbnail', 'projects', $missing);
        $moved += $this->rewriteJson($db, $uploads, 'requirement_levels', 'urls', 'url', 'requirements', $missing);
        $moved += $this->rewriteAdmissions($db, $uploads, $missing);

        CLI::write("Restored {$moved} file URL(s). Missing {$missing}.", $missing ? 'yellow' : 'green');
    }

    protected function rewriteJson($db, UploadService $uploads, string $table, string $column, ?string $key, string $folder, int &$missing): int
    {
        $moved = 0;
        foreach ($db->table($table)->get()->getResultArray() as $row) {
            $items = json_decode((string) ($row[$column] ?? '[]'), true);
            if (!is_array($items)) {
                continue;
            }
            $changed = false;
            foreach ($items as $i => $item) {
                $current = $key ? (is_array($item) ? (string) ($item[$key] ?? '') : '') : (is_string($item) ? $item : '');
                $next = $this->resolve($uploads, $current, $folder);
                if ($next === $current) {
                    if ($current !== '' && !$uploads->isRemoteUrl($current)) {
                        $missing++;
                    }
                    continue;
                }
                if ($key) {
                    if (!is_array($items[$i])) {
                        $items[$i] = [$key => $next];
                    } else {
                        $items[$i][$key] = $next;
                    }
                } else {
                    $items[$i] = $next;
                }
                $changed = true;
                $moved++;
            }
            if ($changed) {
                $db->table($table)->where('id', $row['id'])->update([$column => json_encode($items)]);
            }
        }
        return $moved;
    }

    protected function rewriteAdmissions($db, UploadService $uploads, int &$missing): int
    {
        $moved = 0;
        foreach ($db->table('admissions')->get()->getResultArray() as $row) {
            $docs = json_decode((string) ($row['documents'] ?? '{}'), true);
            if (!is_array($docs)) {
                continue;
            }
            $changed = false;
            foreach (['bulletin', 'other'] as $group) {
                foreach ($docs[$group] ?? [] as $i => $path) {
                    $next = $this->resolve($uploads, (string) $path, 'admissions');
                    if ($next === $path) {
                        if ($path && !$uploads->isRemoteUrl((string) $path)) {
                            $missing++;
                        }
                        continue;
                    }
                    $docs[$group][$i] = $next;
                    $changed = true;
                    $moved++;
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

    protected function resolve(UploadService $uploads, string $path, string $folder): string
    {
        $path = str_replace('\\', '/', trim($path));
        if ($path === '' || $uploads->isRemoteUrl($path)) {
            return $path;
        }

        $local = $uploads->localAbsolutePath($path);
        if ($local) {
            CLI::write("Local {$path}");
            return $uploads->uploadLocalPath($local, $folder);
        }

        $found = $uploads->findCloudinaryByFilename($path);
        if ($found) {
            CLI::write("Cloudinary match {$path}");
            return $found;
        }

        foreach ($this->candidateUrls($path) as $url) {
            $bytes = $uploads->downloadBinary($url);
            if ($bytes === null) {
                continue;
            }
            CLI::write("Downloaded {$url}");
            $tmp = tempnam(sys_get_temp_dir(), 'sjdl');
            file_put_contents($tmp, $bytes);
            try {
                return $uploads->uploadLocalPath($tmp, $folder);
            } finally {
                @unlink($tmp);
            }
        }

        return $path;
    }

    protected function candidateUrls(string $path): array
    {
        $path = ltrim($path, '/');
        $urls = [];
        foreach ($this->origins as $origin) {
            $urls[] = $origin . $path;
            $urls[] = $origin . 'public/' . $path;
            $urls[] = $origin . 'backend/public/' . $path;
            $urls[] = $origin . 'backend/' . $path;
        }
        return $urls;
    }
}
