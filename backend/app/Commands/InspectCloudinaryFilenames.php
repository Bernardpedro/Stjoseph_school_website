<?php

namespace App\Commands;

use Cloudinary\Cloudinary;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InspectCloudinaryFilenames extends BaseCommand
{
    protected $group = 'Cloudinary';
    protected $name  = 'cloudinary:filenames';
    protected $usage = 'cloudinary:filenames';

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

        $res = $cloudinary->adminApi()->assets([
            'max_results' => 30,
            'type'        => 'upload',
        ]);
        foreach ($res['resources'] ?? [] as $asset) {
            $id = $asset['public_id'] ?? '';
            CLI::write(($asset['original_filename'] ?? '-') . ' | ' . ($asset['filename'] ?? '-') . ' | ' . $id);
        }

        $sample = $res['resources'][1]['public_id'] ?? null;
        if ($sample) {
            $detail = $cloudinary->adminApi()->asset($sample);
            CLI::write('DETAIL KEYS: ' . implode(',', array_keys($detail instanceof \ArrayAccess ? iterator_to_array($detail) : (array) $detail)));
            CLI::write('original_filename=' . ($detail['original_filename'] ?? 'none'));
            CLI::write('filename=' . ($detail['filename'] ?? 'none'));
        }
    }
}
