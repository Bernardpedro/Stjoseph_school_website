<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;

class UploadService
{
    public function saveMany(?array $files, string $folder): array
    {
        $saved = [];

        foreach ($files ?? [] as $file) {
            $path = $this->saveOne($file, $folder);
            if ($path) {
                $saved[] = $path;
            }
        }

        return $saved;
    }

    public function saveOne(mixed $file, string $folder): ?string
    {
        if (!$file instanceof UploadedFile) {
            return null;
        }

        if (!$file->isValid() || $file->hasMoved() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file->getSize() <= 0) {
            return null;
        }

        $dir = FCPATH . 'uploads/' . trim($folder, '/') . '/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create upload directory.');
        }

        $ext = $file->getExtension() ?: $file->guessExtension() ?: 'bin';
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
        $file->move($dir, $name, true);

        return 'uploads/' . trim($folder, '/') . '/' . $name;
    }

    public function collect(string $field): array
    {
        $request = service('request');
        if (!$request instanceof IncomingRequest) {
            return [];
        }

        $files = $request->getFileMultiple($field);

        if (!$files) {
            $files = $request->getFileMultiple($field . '[]');
        }

        if (!$files) {
            $one = $request->getFile($field);
            $files = $one ? [$one] : [];
        }

        return is_array($files) ? array_values($files) : [];
    }

    public function toRelative(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        if (preg_match('#uploads/.+$#', $url, $m)) {
            return $m[0];
        }

        return $url;
    }
}
