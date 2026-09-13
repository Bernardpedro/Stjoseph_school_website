<?php

namespace App\Controllers\Api\V1;

use App\Services\ContentCache;
use Config\Database;

class SyncController extends BaseApiController
{
    public function index()
    {
        return $this->ok(ContentCache::remember('sync', 'stamps', function () {
            $db = Database::connect();

            return [
                'events'        => $this->stamp($db, 'events'),
                'announcements' => $this->stamp($db, 'announcements'),
                'galleries'     => $this->stamp($db, 'galleries'),
                'testimonials'  => $this->stamp($db, 'testimonials'),
                'achievements'  => $this->stamp($db, 'achievements'),
                'projects'      => $this->stamp($db, 'projects'),
                'requirements'  => $this->stamp($db, 'requirement_levels'),
                'admissions'    => $this->stamp($db, 'admissions') . '|' . $this->stamp($db, 'admission_requirement_items'),
                'notifications' => $this->stamp($db, 'notifications'),
                'settings'      => $this->stamp($db, 'settings'),
            ];
        }, ContentCache::TTL_SYNC));
    }

    protected function stamp($db, string $table): string
    {
        if (!$db->tableExists($table)) {
            return '0';
        }

        try {
            $row = $db->table($table)
                ->select('COUNT(*) AS total, MAX(id) AS last_id, MAX(updated_at) AS last_update', false)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            $row = $db->table($table)
                ->select('COUNT(*) AS total, MAX(id) AS last_id', false)
                ->get()
                ->getRowArray();
        }

        return implode(':', [
            (string) ($row['total'] ?? 0),
            (string) ($row['last_id'] ?? 0),
            (string) ($row['last_update'] ?? ''),
        ]);
    }
}
