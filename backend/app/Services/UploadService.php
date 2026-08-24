<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;

class UploadService
{
    protected ?Cloudinary $cloudinary = null;

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

        if ($this->isCloudinaryReady()) {
            return $this->saveToCloudinary($file->getTempName(), $folder, $file->getClientName());
        }

        return $this->saveToLocal($file, $folder);
    }

    public function uploadLocalPath(string $absolutePath, string $folder): string
    {
        if (!is_file($absolutePath)) {
            throw new \RuntimeException('Local file not found for Cloudinary upload.');
        }

        return $this->saveToCloudinary($absolutePath, $folder, basename($absolutePath));
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

        $url = trim($url);

        if ($this->isRemoteUrl($url)) {
            return $url;
        }

        if (preg_match('#uploads/.+$#', $url, $m)) {
            return $m[0];
        }

        return $url;
    }

    public function isCloudinaryReady(): bool
    {
        return trim((string) env('CLOUDINARY_CLOUD_NAME')) !== ''
            && trim((string) env('CLOUDINARY_API_KEY')) !== ''
            && trim((string) env('CLOUDINARY_API_SECRET')) !== '';
    }

    public function isRemoteUrl(?string $url): bool
    {
        return is_string($url) && preg_match('#^https?://#i', $url) === 1;
    }

    public function localAbsolutePath(string $relative): ?string
    {
        $relative = str_replace('\\', '/', $relative);
        if (!preg_match('#uploads/.+$#', $relative, $m)) {
            return null;
        }

        $absolute = FCPATH . $m[0];
        return is_file($absolute) ? $absolute : null;
    }

    public function uploadFromUrl(string $url, string $folder, ?string $originalName = null): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'sjmedia');
        $bytes = $this->downloadBinary($url);
        if ($bytes === null) {
            throw new \RuntimeException('Remote media is not a real file.');
        }
        file_put_contents($tmp, $bytes);
        try {
            return $this->saveToCloudinary($tmp, $folder, $originalName);
        } finally {
            @unlink($tmp);
        }
    }

    public function findCloudinaryByFilename(string $filename): ?string
    {
        $filename = basename(str_replace('\\', '/', $filename));
        if ($filename === '') {
            return null;
        }

        try {
            $res = $this->client()->searchApi()
                ->expression('filename:' . $filename)
                ->maxResults(1)
                ->execute();
        } catch (\Throwable $e) {
            log_message('error', 'Cloudinary search failed: ' . $e->getMessage());
            return null;
        }

        $hit = $res['resources'][0] ?? null;
        return is_array($hit) ? ($hit['secure_url'] ?? $hit['url'] ?? null) : null;
    }

    public function downloadBinary(string $url): ?string
    {
        $ctx = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'timeout'       => 20,
                'ignore_errors' => true,
                'header'        => "Accept: image/*,application/pdf,*/*\r\nUser-Agent: StJosephMediaSync/1.0\r\n",
            ],
            'https' => [
                'method'        => 'GET',
                'timeout'       => 20,
                'ignore_errors' => true,
                'header'        => "Accept: image/*,application/pdf,*/*\r\nUser-Agent: StJosephMediaSync/1.0\r\n",
            ],
        ]);
        $body = @file_get_contents($url, false, $ctx);
        if (!is_string($body) || $body === '') {
            return null;
        }
        if (!$this->looksLikeFile($body)) {
            return null;
        }
        return $body;
    }

    protected function looksLikeFile(string $body): bool
    {
        if (str_starts_with(ltrim($body), '{') || str_starts_with(ltrim($body), '<!')) {
            return false;
        }
        $sig = substr($body, 0, 8);
        return str_starts_with($sig, "\xFF\xD8\xFF")
            || str_starts_with($sig, "\x89PNG")
            || str_starts_with($sig, "GIF8")
            || str_starts_with($sig, '%PDF')
            || str_starts_with($sig, 'RIFF');
    }

    protected function saveToCloudinary(string $path, string $folder, ?string $originalName = null): string
    {
        if (!is_file($path)) {
            throw new \RuntimeException('Upload file is missing.');
        }

        $root = trim((string) env('CLOUDINARY_FOLDER', 'stjoseph'), '/');
        $section = trim($folder, '/');
        $cloudFolder = $section !== '' ? $root . '/' . $section : $root;

        $options = [
            'folder'           => $cloudFolder,
            'resource_type'    => 'auto',
            'unique_filename'  => true,
            'overwrite'        => false,
            'use_filename'     => (bool) $originalName,
            'chunk_size'       => 6_000_000,
        ];

        try {
            $result = $this->client()->uploadApi()->upload($path, $options);
        } catch (\Throwable $e) {
            log_message('error', 'Cloudinary upload failed: ' . $e->getMessage());
            if (stripos($e->getMessage(), 'missing permissions') !== false) {
                throw new \RuntimeException(
                    'Cloudinary API key cannot upload files. In Cloudinary Settings → API Keys, assign this key a role that can create/upload assets, or use the product environment root API key.'
                );
            }
            throw new \RuntimeException('Could not upload the file to Cloudinary.');
        }

        $url = $result['secure_url'] ?? $result['url'] ?? null;
        if (!$url) {
            throw new \RuntimeException('Cloudinary did not return a file URL.');
        }

        return (string) $url;
    }

    protected function saveToLocal(UploadedFile $file, string $folder): string
    {
        $dir = FCPATH . 'uploads/' . trim($folder, '/') . '/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create upload directory.');
        }

        $ext = $file->getExtension() ?: $file->guessExtension() ?: 'bin';
        $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
        $file->move($dir, $name, true);

        return 'uploads/' . trim($folder, '/') . '/' . $name;
    }

    protected function client(): Cloudinary
    {
        if ($this->cloudinary instanceof Cloudinary) {
            return $this->cloudinary;
        }

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => trim((string) env('CLOUDINARY_CLOUD_NAME')),
                'api_key'    => trim((string) env('CLOUDINARY_API_KEY')),
                'api_secret' => trim((string) env('CLOUDINARY_API_SECRET')),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);

        return $this->cloudinary;
    }
}
