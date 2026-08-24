<?php

namespace App\Commands;

use App\Services\UploadService;
use Cloudinary\Cloudinary;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class PingCloudinary extends BaseCommand
{
    protected $group       = 'Cloudinary';
    protected $name        = 'cloudinary:ping';
    protected $description = 'Check that Cloudinary credentials in .env work.';
    protected $usage       = 'cloudinary:ping';

    public function run(array $params)
    {
        $uploads = new UploadService();
        if (!$uploads->isCloudinaryReady()) {
            CLI::error('Cloudinary credentials are missing in .env');
            return;
        }

        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => trim((string) env('CLOUDINARY_CLOUD_NAME')),
                    'api_key'    => trim((string) env('CLOUDINARY_API_KEY')),
                    'api_secret' => trim((string) env('CLOUDINARY_API_SECRET')),
                ],
                'url' => ['secure' => true],
            ]);
            $cloudinary->adminApi()->ping();
            CLI::write('Cloudinary connection is working.', 'green');
        } catch (\Throwable $e) {
            CLI::error('Cloudinary connection failed.');
            log_message('error', 'Cloudinary ping failed: ' . $e->getMessage());
        }
    }
}
