<?php

namespace App\Commands;

use Cloudinary\Cloudinary;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ListCloudinaryAssets extends BaseCommand
{
    protected $group       = 'Cloudinary';
    protected $name        = 'cloudinary:list';
    protected $description = 'List Cloudinary assets that may match school images.';
    protected $usage       = 'cloudinary:list';

    public function run(array $params)
    {
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => trim((string) env('CLOUDINARY_CLOUD_NAME')),
                'api_key'    => trim((string) env('CLOUDINARY_API_KEY')),
                'api_secret' => trim((string) env('CLOUDINARY_API_SECRET')),
            ],
            'url' => ['secure' => true],
        ]);

        $next = null;
        $shown = 0;
        do {
            $opts = ['max_results' => 100, 'type' => 'upload'];
            if ($next) {
                $opts['next_cursor'] = $next;
            }
            $res = $cloudinary->adminApi()->assets($opts);
            foreach ($res['resources'] ?? [] as $asset) {
                $id = $asset['public_id'] ?? '';
                $url = $asset['secure_url'] ?? '';
                CLI::write($id . ' | ' . $url);
                $shown++;
            }
            $next = $res['next_cursor'] ?? null;
        } while ($next && $shown < 400);

        CLI::write("Shown {$shown} assets.", 'green');
    }
}
